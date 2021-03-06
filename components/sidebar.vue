<template>
    <div class="col-md-4 sidebar-id">
        <!-- search bar -->
        <card
            :title="$t('sidebar.search_title')"
            header-cls="bg-primary text-light text-capitalize"
            class="card mb-3"
        >
            <div class="form">
                <div class="form-group">
                    <input
                        type="text"
                        id="search-inp"
                        class="form-control"
                        :placeholder="$t('sidebar.search_placeholder')"
                        v-model.trim="searchKey"
                        @keypress.enter="goToFind"
                    />
                </div>
                <div class="form-group">
                    <nuxt-link
                        class="btn btn-primary"
                        tag="button"
                        :disabled="!searchKey.length"
                        :to="
                            localePath({
                                path: '/blog/find',
                                query: {
                                    q: this.searchKey,
                                    page: 1,
                                },
                            })
                        "
                    >
                        {{ $t('sidebar.search_btn') }}
                    </nuxt-link>
                </div>
            </div>
        </card>

        <!-- popular posts -->
        <card
            :title="$t('sidebar.pop_title')"
            header-cls="bg-primary text-light text-capitalize"
            cls="mb-3"
        >
            <div class="row">
                <div
                    class="col-12"
                    v-for="lod in loadingArr"
                    :key="lod + Math.random()"
                >
                    <content-loader
                        :width="240"
                        :height="60"
                        primaryColor="#dbdbdb"
                        secondaryColor="#6b6b6b"
                    >
                        <rect
                            x="2"
                            y="10"
                            rx="6"
                            ry="6"
                            width="51"
                            height="42"
                        ></rect>
                        <rect
                            x="65"
                            y="19"
                            rx="0"
                            ry="0"
                            width="102"
                            height="11"
                        ></rect>
                        <rect
                            x="65"
                            y="40"
                            rx="0"
                            ry="0"
                            width="46"
                            height="12"
                        ></rect>
                        <rect
                            x="127"
                            y="40"
                            rx="0"
                            ry="0"
                            width="46"
                            height="12"
                        ></rect>
                    </content-loader>
                </div>
            </div>
            <div class="row">
                <div
                    class="media col-12 my-2"
                    v-for="p in popPosts"
                    :key="p.id + Math.random()"
                >
                    <img
                        :src="`/img/posts/${p.img}`"
                        class="mr-3 rounded"
                        :alt="p.id"
                        width="70"
                        height="70"
                    />
                    <div class="media-body">
                        <h5 class="mt-0">
                            <nuxt-link :to="localePath('/blog/' + p.slug)">
                                {{ p.title }}
                            </nuxt-link>
                        </h5>
                        <p class="mt-1">
                            <span
                                class="badge badge-info p-1"
                                data-toggle="tooltip"
                                data-placement="top"
                                :title="$t('post_list.last_update')"
                            >
                                <i class="fas fa-clock"></i>
                                {{ p.updated }}
                            </span>
                            <nuxt-link
                                :to="
                                    localePath('/blog/' + p.slug + '/#comments')
                                "
                                class="badge badge-info p-1 text-light"
                                data-toggle="tooltip"
                                data-placement="top"
                                :title="$t('post_list.comment_count')"
                            >
                                <i class="fas fa-comment-alt"></i>
                                {{ p.comments_count }}
                            </nuxt-link>
                        </p>
                    </div>
                </div>
            </div>
        </card>

        <!-- share this -->
        <card
            :title="$t('sidebar.share')"
            header-cls="bg-primary text-light text-capitalize"
            cls="mb-3"
        >
            <a
                :href="'http://www.facebook.com/sharer.php?u=' + url.self"
                target="_blank"
                class="btn btn-outline-primary m-2"
            >
                <i class="fab fa-facebook-f pr-3"></i>
                {{ $t('sidebar.fb') }}
            </a>
            <a
                :href="
                    'https://twitter.com/share?url=' +
                    url.self +
                    '&amp;hashtags=' +
                    url.keys
                "
                class="btn btn-outline-info m-2 noColor"
                target="_blank"
            >
                <i class="fab fa-twitter pr-3"></i>
                {{ $t('sidebar.twitter') }}
            </a>
            <a
                :href="
                    'https://www.linkedin.com/shareArticle?mini=true&url=' +
                    url.self
                "
                class="btn btn-outline-primary m-2"
                target="_blank"
            >
                <i class="fab fa-linkedin-in pr-3"></i>
                {{ $t('sidebar.linked') }}
            </a>
            <a
                :href="'mailto:webmaster@example.com?subject=' + url.self"
                class="btn btn-outline-secondary m-2"
            >
                <i class="fas fa-mail-bulk pr-3"></i>
                {{ $t('sidebar.mail_to') }}
            </a>
        </card>

        <!-- categories section -->
        <card
            :title="$t('sidebar.cat_title')"
            header-cls="bg-primary text-light text-capitalize text-center"
            cls="mb-3"
        >
            <div class="text-center" v-if="loadCats">
                <span>
                    <i
                        class="fa fa-spinner fa-pulse fa-3x fa-fw align-content-center"
                    ></i>
                </span>
            </div>
            <nuxt-link
                v-for="c in cats"
                :key="c.slug"
                :to="localePath('/blog/categories/' + c.slug)"
                class="btn btn-primary m-1"
            >
                {{ c.title }}
                <span class="badge badge-light">
                    {{ c.posts_count }}
                </span>
            </nuxt-link>
        </card>
    </div>
</template>
<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
// @ts-ignore
import { ContentLoader } from 'vue-content-loader'
import PostInterface from '~/interfaces/PostInterface'
import CategoryInterface from '~/interfaces/category-interface'
import Card from '~/components/card.vue'

const getData = (path: string) =>
    import(`~/data/${path}/1.json`).then((m) => m.default || m)

@Component({
    components: { ContentLoader, Card },
})
export default class Sidebar extends Vue {
    public popPosts: PostInterface[] = []
    public cats: CategoryInterface[] = []
    public loadingArr: number[] = Array(4).fill(Math.random())
    public url = {
        self: location.href,
        keys: 'programming,php,vueJs,web_development,laravel',
    }
    public loadCats: boolean = true
    public searchKey: string = ''

    public async loadPopPosts() {
        const res = await getData('popular').finally(() =>
            this.loadCategoriesList()
        )
        if (!res) {
            this.$nf.error()
            return
        }

        setTimeout((_) => {
            this.popPosts = res
            this.loadingArr.splice(0)
        }, 900)
    }

    public async loadCategoriesList() {
        // this.$axios.$get('categories').then((res) => {
        const res = await getData('categories')
        this.loadCats = false
        if (!res) {
            this.$nf.error()
            return
        }

        this.cats = res
        // })
    }

    public goToFind() {
        location.href = this.localePath({
            path: '/blog/find',
            query: {
                q: this.searchKey,
                // @ts-ignore
                page: 1,
            },
        })
    }

    mounted() {
        this.loadPopPosts()

        this.searchKey = (this.$route.query.q as string) ?? ''
    }
}
</script>
<style lang="scss" scopped>
.sidebar-id .card-footer {
    display: none;
}
</style>
