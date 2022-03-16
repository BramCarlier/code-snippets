<template>
    <div class="items-center" id="user">
        <div class="flex justify-between items-center px-5">
            <img alt="User icon" src="/images/base-layout/icons/nav/user.png">
            <p class="font-primary-bold" v-html="displayName" v-if="user.first_name && user.last_name"/>
            <span class="icon arrow-down"/>
        </div>
        <div class="menu-container">
            <ul class="p-0 text-center" id="user-menu">
                <li :class="link.children || link.languages ? 'subitem toggle-left' : ''"
                    v-for="(link, index) in links"
                    :key="index">
                    <a :class="link.children || link.languages ? 'toggle-icon' : 'flex justify-center'"
                       :href="link.path || '#'"
                       :id="link.name">
                        <span class="text-inherit"
                              v-text="link.name"/>
                    </a>
                    <ul :id="link.name + '-select'"
                        class="submenu pl-0 bg-primary-300 text-left">
                        <template v-if="link.children">
                            <li v-for="(child, index) in link.children" :key="index">
                                <a :href="child.path || '#'" v-html="child.name || child"/>
                            </li>
                        </template>
                        <template v-if="link.languages">
                            <li v-for="(language, key) in link.languages" :key="key">
                                <a @click="updateLanguage(key)" v-html="language"/>
                            </li>
                        </template>
                    </ul>
                </li>
                <li class="border-2 border-primary-400">
                    <a :href="logout" id="signout">
                        <span v-text="'sign out'"/>
                        <span class="icon sign-out ml-2"/>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
    import Formatter from '../../utils/Formatter';

    export default {
        name: 'User',

        created() {
            this.loadUser();
        },

        methods: {
            loadUser() {
                return this.vueTemplateStore.dispatch('users/loadUser');
            },
            updateLanguage(lang) {
                return this.vueTemplateStore.dispatch('users/updateLanguage', lang);
            },
        },
        computed: {
            links() {
                return this.$userOptions.Links;
            },
            user() {
                return this.vueTemplateStore.state.users.user;
            },
            displayName() {
                return Formatter.capitalizeFirstLetter(this.user.first_name) + ' '
                    + Formatter.capitalizeFirstLetter(this.user.last_name.substr(0, 1)) + '.';
            },
            logout: () => (process.env.MIX_DOMAIN_PROTOCOL || 'http://')
                + 'account'
                + (process.env.MIX_DOMAIN_URL || '.ci-web.local')
                + '/logout'
        }
    };
</script>
