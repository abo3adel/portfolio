<template>
    <div>
        <div class="text-center w-100" v-if="loading">
            <div
                class="spinner-border text-info"
                role="status"
                style="width: 3rem; height: 3rem;"
            >
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- comment list -->
        <div
            class="media mt-2 border-top pt-2"
            v-for="(c, cinx) in comments"
            :key="c.id + '-' + Math.random()"
        >
            <img
                :src="c.gravatar"
                class="mr-3 img-thumbnail"
                :alt="c.user_name"
                width="100"
                height="100"
            />
            <div class="media-body">
                <p>
                    <nuxt-link :to="localePath(`/blog/${c.post.slug}`)">
                        {{ c.post.title }}
                    </nuxt-link>
                </p>
                <h5 class="mt-0">
                    {{ c.user_name }}
                </h5>
                <span class="badge badge-primary p-1">
                    {{ c.updated }}
                </span>
                <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    @click.prevent="remove(c.id, cinx)"
                >
                    <i
                        :id="`spinner${c.id}`"
                        class="fas fa-trash-alt"
                        role="status"
                        aria-hidden="true"
                    ></i>
                    DELETE
                </button>
                <p>
                    {{ c.body }}
                </p>
            </div>
        </div>

        <!-- TODO show pagination -->
    </div>
</template>
<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import CommentInterface from '~/interfaces/comments-interface'

@Component({
    // auth: false,
    head() {
        return {
            title: (this as CommentList).title || '',
        }
    },
})
export default class CommentList extends Vue {
    public loading: boolean = true
    public comments: CommentInterface[] = []

    public async loadComments(page: number = 1) {
        this.loading = true
        const res = await this.$axios.$get(`root/comments?page=${page}`)

        this.loading = false

        if (!res || !res.data) {
            this.$nf.error()
            return
        }

        this.comments = res.data
    }

    public async remove(id: number, index: number): void {
        const loader = document.querySelector(`#spinner${id}`) as HTMLDivElement
        loader.className = 'spinner-border spinner-border-sm align-middle'

        const res = await this.$axios.$delete(`root/comments/${id}`)

        loader.className = 'fas fa-trash-alt'

        if (res) {
            this.$nf.error()
            return
        }

        // remove from comments list
        this.comments.splice(index, 1)
    }

    get title(): string {
        return this.$i18n.t('admin.title.comments') as string
    }

    mounted() {
        this.loadComments()
    }
}
</script>
<style lang="scss"></style>