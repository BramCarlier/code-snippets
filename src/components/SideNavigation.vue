<template>
    <div>
        <button @click="toggleShowMenu"
                class="outline-none focus:outline-none w-10 h-10 rounded-full bg-primary-600 hover:bg-primary-300 border-0 shadow"
                id="navigationMenuToggle">
            <i :class="`text-white fa mx-auto ${showNavigation ? 'fa-chevron-left' : 'fa-chevron-right'}`"/>
        </button>
        <div class="nav-container">
            <ul class="nav py-5">
                <template v-for="(item, index) in pages">
                    <li :class="`navigation__item flex flex-col content-center items-center text-gray-500 mx-0 cursor-pointer hover:bg-gray-100 ${item.active ? 'active' : ''}`"
                        :key="index">
                        <div @click="toggleActiveState(index)"
                             class="navigation__label px-8 py-6 w-full">
                            <span class="text-xl" v-html="capitalizeFirstLetter(item.name)"/>
                            <i :class="`fa ${item.active ? 'fa-chevron-up' : 'fa-chevron-down'}`"/>
                        </div>
                        <ul class="w-full px-12 submenu overflow-hidden p-0 cursor-default relative px-6">
                            <template v-if="item.children">
                                <li class="text-gray-500 mx-0 items-center my-5"
                                    v-for="(child, index) in item.children"
                                    :key="index">
                                    <a :class="`h-full w-full items-center cursor-pointer ${activeOrDisabled(child)}`"
                                       :href="child.path"
                                       v-if="child.path">
                                        <span v-html="capitalizeFirstLetter(child.name)"/>
                                    </a>
                                    <span class="inline-block font-primary-bold cursor-default"
                                          v-else
                                          v-html="capitalizeFirstLetter(child.name)"/>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>
            </ul>
        </div>
    </div>
</template>

<script>
    import Formatter from '../utils/Formatter.js';

    export default {
        name: 'SideNavigation',

        data: () => ({
            showNavigation: localStorage.getItem('showNavigation') === 'true',
        }),

        created() {
            this.loadPages(this.$userOptions.Pages);
            this.checkShowMenu();
        },

        methods: {
            loadPages(pages) {
                return this.vueTemplateStore.dispatch('navigation/loadPages', pages);
            },

            toggleActiveState(page) {
                return this.vueTemplateStore.dispatch('navigation/toggleActiveState', page);
            },

            activeOrDisabled(child) {
                let linkClass = '';
                linkClass += child.active ? ' text-secondary-600' : ' text-gray-500';
                linkClass += child.disabled ? ' opacity-50 pointer-events-none' : '';
                return linkClass;
            },

            // add hide-navigation class to the app body
            toggleShowMenu() {
                document.getElementsByTagName('body')[0].classList.toggle('hide-navigation');
                localStorage.setItem('showNavigation', (!this.showNavigation).toString());
            },

            // add hide-navigaiton class when show is false
            checkShowMenu() {
                if (!this.showNavigation) {
                    document.getElementsByTagName('body')[0].classList.add('hide-navigation');
                }
            },
            capitalizeFirstLetter: Formatter.capitalizeFirstLetter,
        },

        computed: {
            pages() {
                return this.vueTemplateStore.state.navigation.pages;
            },
        }
    };
</script>
