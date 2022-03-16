<template>
    <div class="flex items-center relative" id="projects">
        <div class="flex justify-between items-center px-5">
            <img alt="Projects icon" src="/images/base-layout/icons/nav/projects.png"/>
            <p class="font-primary-bold" v-html="'my projects'"/>
            <span class="icon arrow-down"/>
        </div>
        <div class="bg-primary-300 hidden fixed right-0 top-0 shadow w-auto" id="projects-menu">
            <div class="projects-container">
                <h5 class="m-8 text-changeable" v-html="'choose project'"/>
                <div class="w-full mt-6 ml-0 pt-6 pr-6 border-b border-gray-200 bg-white">
                    <label class="checkbox-container ml-10 right-0 w-full" for="project-all" id="check-all-projects">
                        <input :checked="areAllProjectsChecked"
                               @change="selectOrDeselectAllProjects"
                               class="custom-checkbox"
                               id="project-all"
                               name="project[]"
                               type="checkbox"/>
                        <span v-html="'select/deselect all projects'"/>
                        <span/>
                    </label>
                </div>
                <div class="px-10 py-6 font-secondary bg-white overflow-auto"
                     style="max-height: 300px">
                    <fieldset>
                        <div>
                            <label :for="'customers-'+customer.id"
                                   class="checkbox-container"
                                   v-for="customer in customers"
                                   :key="customer.id">
                                <input :id="`customers-${customer.id}`"
                                       :value="customer.id"
                                       class="custom-checkbox"
                                       name="project[]"
                                       type="checkbox"
                                       v-model="selectedProjectIds"/>
                                <span v-html="customer.attributes.name"/>
                                <span/>
                            </label>
                        </div>
                        <div class="flex justify-start mt-8">
                            <button @click="applyProjectScope"
                                    class="button standard mb-5 -mt-5"
                                    type="submit"
                                    v-if="!loading"
                                    v-html="'apply'"/>
                            <button class="button standard inverse h-auto pointer-events-none border-none inline-block"
                                    disabled
                                    v-html="generatedProjectCountMessage"/>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Customer',

        created() {
            this.vueTemplateStore.dispatch('customers/fetchCustomers');
        },

        methods: {
            selectOrDeselectAllProjects() {
                if (this.areAllProjectsChecked) {
                    this.deselectAllProjects();

                    return;
                }

                this.deselectAllProjects();
                this.selectAllProjects();
            },

            selectAllProjects() {
                for (const customer of this.customers) {
                    this.vueTemplateStore.commit('users/ADD_PROJECT', customer.id);
                }
            },

            deselectAllProjects() {
                this.vueTemplateStore.commit('users/SET_PROJECTS', []);
            },

            applyProjectScope() {
                this.vueTemplateStore.dispatch('users/updateProjects');
            }
        },

        computed: {
            user() {
                return this.vueTemplateStore.state.users.user;
            },

            customers() {
                return this.vueTemplateStore.state.customers.customers;
            },

            selectedProjectIds: {
                get: function () {
                    return this.user.selected_projects;
                },

                set: function (customers) {
                    this.vueTemplateStore.commit('users/SET_PROJECTS', customers);
                }
            },

            generatedProjectCountMessage() {
                return `${this.selectedProjectIds.length} selected projects'}`;
            },

            areAllProjectsChecked() {
                return this.selectedProjectIds.length === this.customers.length;
            },

            loading() {
                return this.vueTemplateStore.state.users.loading;
            }
        }
    };
</script>
