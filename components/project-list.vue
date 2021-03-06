<template>
    <div>
        <div>
            <ul class="nav nav-pills">
                <li
                    class="nav-item"
                    v-for="type in projectsTypes"
                    :key="type.txt"
                >
                    <a
                        class="nav-link"
                        href="#"
                        :class="{ active: activeType === type.val }"
                        @click.stop.prevent="filterProjects(type.val)"
                    >
                        {{ type.txt }}
                    </a>
                </li>
            </ul>
            <div
                class="alert alert-warning my-2"
                v-if="projectsTypes[3].val !== activeType"
            >
                <strong v-text="$t('home.alert.note')"></strong>
                <span v-text="$t('home.alert.alertMessage')"></span>
            </div>
            <div class="mt-3">
                <div class="row">
                    <div
                        class="col-12 col-sm-6 col-md-4 col-xl-4 mb-3"
                        v-for="inp in projectsLoad"
                        :key="inp * Math.random()"
                    >
                        <content-loader
                            class="card card-body bg-transparent border-light p-0 m-0"
                            :width="240"
                            :height="130"
                            primaryColor="#dbdbdb"
                            secondaryColor="#6b6b6b"
                        >
                            <rect
                                x="9"
                                y="25"
                                rx="4"
                                ry="4"
                                width="225"
                                height="15"
                            />
                            <rect
                                x="9"
                                y="65"
                                rx="4"
                                ry="4"
                                width="90"
                                height="18"
                            />
                            <rect
                                x="115"
                                y="65"
                                rx="4"
                                ry="4"
                                width="120"
                                height="18"
                            />
                            <rect
                                x="15"
                                y="105"
                                rx="4"
                                ry="4"
                                width="63"
                                height="19"
                            />
                            <rect
                                x="90"
                                y="105"
                                rx="4"
                                ry="4"
                                width="63"
                                height="19"
                            />
                            <rect
                                x="165"
                                y="105"
                                rx="4"
                                ry="4"
                                width="63"
                                height="19"
                            />
                        </content-loader>
                    </div>
                </div>
                <!-- TODO animate projects array -->
                <transition-group name="projects" tag="div" class="row">
                    <div
                        class="col-12 col-sm-6 col-md-3 mb-3 project-card"
                        v-for="project in projects"
                        :key="project.id + project.title"
                    >
                        <card :overlay="true" cls="text-left">
                            <template slot="img">
                                <span
                                    class="projectType badge badge-danger text-uppercase position-absolute"
                                    style="top: 0; right: 0;"
                                >
                                    {{ project.type }}
                                </span>
                                <img
                                    v-lazy="'/img/projects/' + project.img[0]"
                                    class="card-img"
                                />
                            </template>
                            <h5 class="card-title">
                                <a
                                    :href="project.link"
                                    target="_blank"
                                    class="text-light"
                                >
                                    {{ project.title }}
                                </a>
                            </h5>
                            <p class="card-text">
                                <strong class="text-capitalize"
                                    >{{ $t('home.project.client') }}:</strong
                                >
                                <span class="text-capitalize">
                                    {{ project.client }}
                                </span>
                            </p>
                            <p class="card-text text-center">
                                <a
                                    :href="project.link"
                                    class="btn btn-primary btn-sm mb-2"
                                    target="_blank"
                                >
                                    <i class="fas fa-link"></i>
                                    {{ $t('home.project.visit') }}
                                </a>
                                <button
                                    class="btn btn-info btn-sm mb-2"
                                    @click.prevent="openModal(project.id)"
                                >
                                    <span
                                        :id="'spinnerView' + project.id"
                                        class="spinner-border spinner-border-sm d-none"
                                        role="status"
                                        aria-hidden="true"
                                    ></span>
                                    <i class="fas fa-info"></i>
                                    {{ $t('home.project.more_info') }}
                                </button>
                                <a
                                    :href="project.download_url"
                                    class="btn btn-primary btn-sm mb-2"
                                    target="_blank"
                                    v-if="project.download_url.length"
                                >
                                    <i class="fas fa-download"></i>
                                    {{ $t('home.project.download') }}
                                </a>
                            </p>
                            <p class="card-text" v-if="auth">
                                <button
                                    class="btn btn-warning mx-1 mb-2 text-uppercase"
                                    @click.prevent="$emit(`edit`, project)"
                                >
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>
                                <button
                                    class="btn btn-danger mx-1 mb-2 text-uppercase"
                                    @click.prevent="$emit('delete', project.id)"
                                >
                                    <i
                                        :id="`del${project.id}`"
                                        class="fas fa-trash-alt"
                                    ></i>
                                    Delete
                                </button>
                            </p>
                            <template slot="footer">
                                <div class="text-center">
                                    <span
                                        class="badge badge-primary p-1 m-1"
                                        v-for="t in project.tags"
                                        :key="
                                            t.id + project.title + Math.random()
                                        "
                                    >
                                        {{ t.title }}
                                    </span>
                                </div>
                            </template>
                        </card>
                    </div>
                </transition-group>
            </div>
        </div>

        <!-- Project Modal -->
        <div
            class="modal fade"
            id="modalProject"
            tabindex="-1"
            role="dialog"
            aria-labelledby="modalProjectLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content bg-dark">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title" id="modalProjectLabel">
                            {{ mp.title }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- product image carsoul -->
                        <div
                            id="project-carousel"
                            class="carousel slide"
                            data-ride="carousel"
                        >
                            <div class="carousel-inner">
                                <div
                                    ref="slide"
                                    class="carousel-item"
                                    v-for="(i, index) in mp.img"
                                    :key="i + Math.random()"
                                    :class="{ active: index === 0 }"
                                    data-interval="5000"
                                >
                                    <img
                                        :src="'/img/projects/' + i"
                                        class="d-block w-100 p-img"
                                        :alt="mp.title"
                                    />
                                </div>
                            </div>
                            <a
                                class="carousel-control-prev"
                                href="#project-carousel"
                                role="button"
                                data-slide="prev"
                                @click.prevent="prev"
                            >
                                <span
                                    class="carousel-control-prev-icon"
                                    aria-hidden="true"
                                ></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a
                                class="carousel-control-next"
                                href="#project-carousel"
                                role="button"
                                data-slide="next"
                                @click.prevent="next"
                            >
                                <span
                                    class="carousel-control-next-icon"
                                    aria-hidden="true"
                                ></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                        <p class="pt-3">
                            {{ mp.info }}
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-danger"
                            data-dismiss="modal"
                        >
                            {{ $t('home.modal.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
import { Vue, Component, Prop, Ref } from 'vue-property-decorator'
import ProjectInterface from '~/interfaces/project-interface'
import Card from '~/components/card.vue'
// @ts-ignore
import { ContentLoader } from 'vue-content-loader'

const getProjects = () =>
    import('~/data/projects/1.json').then((m) => m.default || m)

export const defaultProject: ProjectInterface = {
    id: 0,
    title: '',
    client: '',
    img: [],
    link: '',
    info: '',
    updated_at: '',
    type: '',
    tags: [],
}

@Component({
    components: {
        Card,
        ContentLoader,
    },
})
export default class ProjectList extends Vue {
    @Ref('slide') readonly slides!: HTMLDivElement[]

    @Prop({ type: Boolean, default: false }) readonly auth!: boolean

    public projects: ProjectInterface[] = []
    public allProjects: ProjectInterface[] = []
    public projectsLoad: number[] = Array(5).fill(3)
    public mp: ProjectInterface = defaultProject
    public activeType: string = 'all'
    public projectsTypes: {
        txt: any
        val: string
    }[] = [
        {
            txt: this.$i18n.t('home.pills.all'),
            val: 'all',
        },
        {
            txt: this.$i18n.t('home.pills.laravel'),
            val: 'laravel',
        },
        {
            txt: this.$i18n.t('home.pills.spa'),
            val: 'spa',
        },
        {
            txt: this.$i18n.t('home.pills.mobile'),
            val: 'mobile',
        },
    ]
    public carousel = {
        current: 1,
        amount: this.mp.img.length,
        interval: {},
    }

    public async loadProjects() {
        const res = await getProjects()

        this.projectsLoad.splice(0)

        if (!res) {
            this.$nf.error()
            return
        }

        this.allProjects = res
        this.projects = [...res]
    }

    /**
     * filter projects based on selected tap
     *
     * 0 => all
     * 1 => laravel
     * 2 => spa
     * 3 => mobile
     */
    public filterProjects(type: string = '') {
        if (!this.allProjects.length) return

        this.projects.splice(0)
        this.projectsLoad = Array(5).fill(3)

        this.activeType = type
        if (type === 'all') {
            this.projects = [...this.allProjects]
            this.projectsLoad.splice(0)
            return
        }

        const filterd = this.allProjects.filter((x) => x.type === type)
        this.projects = [...filterd]
        this.projectsLoad.splice(0)
    }

    /**
     * remove an item from list
     */
    public removeItem(id: number): void {
        this.allProjects = [...this.allProjects.filter((x) => x.id !== id)]

        this.filterProjects(this.activeType)
    }

    public addItem(item: ProjectInterface, exists: boolean = false): void {
        if (exists) {
            this.allProjects = this.allProjects.map((x) => {
                if (x.id === item.id) {
                    x = item
                }
                return x
            })
        } else {
            this.allProjects.unshift(item)
        }

        this.filterProjects(this.activeType)
    }

    /**
     * open project modal
     */
    public openModal(id: number = 0) {
        const project = this.projects.filter((x) => x.id === id)
        Object.assign(this.mp, project[0])

        this.carousel.amount = project[0].img.length

        // @ts-ignore
        new Modal(document.querySelector(`#modalProject`)).show()

        this.carousel.current = 1
        this.carousel.interval = setInterval(() => {
            this.next()
        }, 5000)
    }

    /**
     * carsousel controllers
     */
    public resetInterval() {
        // @ts-ignore
        clearInterval(this.carousel.interval)
        this.carousel.interval = setInterval(() => {
            this.next()
        }, 5000)
    }
    /**
     * carousel move slide
     */
    public move(inx: number) {
        this.slides.forEach((x, index) => {
            x.classList.remove('active')

            if (index === inx) {
                x.classList.add('active')
            }
        })

        //reset interval timer
        this.resetInterval()
    }

    /**
     * show next carousel slide
     */
    public next() {
        if (this.carousel.current >= this.carousel.amount) {
            this.carousel.current = 0
        }
        this.move(this.carousel.current++)
    }

    /**
     * show previous carousel slide
     */
    public prev() {
        if (this.carousel.current < 1) {
            this.carousel.current = this.carousel.amount
        }
        this.move(--this.carousel.current)
    }

    mounted() {
        this.loadProjects()
        ;(document.getElementById(
            'modalProject'
        ) as HTMLDivElement).addEventListener('hidden.bs.modal', () => {
            // @ts-ignore
            clearInterval(this.carousel.interval)
        })
    }

    destroyed() {
        // @ts-ignore
        clearInterval(this.carousel.interval)
        ;(document.getElementById(
            'modalProject'
        ) as HTMLDivElement).removeEventListener('hidden.bs.modal', () => {})
    }
}
</script>
<style lang="scss" scoped>
.project-card {
    transition: all 1s;
    .card-img {
        width: 100%;
        height: 30vh;
    }
}
.projects-enter,
.projects-leave-to {
    opacity: 0;
    transform: translateY(30px);
}
.projects-leave-active {
    position: absolute;
}
</style>
<style lang="scss">
@import '~assets/css/media';

.project-card .card-img {
    @include media('>sm') {
        height: 30vh !important;
    }
    @include media('>md') {
        height: 35vh !important;
    }
    @include media('>lg') {
        height: 40vh;
    }
}

.p-img {
    height: 40vh;
    @include media('>sm') {
        height: 40vh;
    }
    @include media('>md') {
        height: 60vh;
    }
}
</style>
