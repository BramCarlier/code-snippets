<template>
    <nav class="flex justify-between" id="top-navigation">
        <div class="flex">
            <user/>
            <customer/>
        </div>
    </nav>
</template>
<script>
    export default {
        name: 'Banner',

        methods: {
            activate() {
                this.$refs['menu-toggle'].classList.toggle('active');
            }
        },
    };
</script>
