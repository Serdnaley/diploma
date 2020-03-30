<template>
    <div>
        <el-dialog
            v-bind="{ visible, ...$attrs }"
            v-on="{ ...$listeners, close }">
            <slot/>
        </el-dialog>
    </div>
</template>

<script>
    /**
     * Important
     *
     * When root element is <el-dialog> JS says:
     * [Vue warn]: Error in nextTick: "TypeError: Cannot read property 'insert' of undefined"
     *
     * Root <div> (or another tag) is required!
     */

    export default {
        name: "RouteModal",

        props: {
            backTo: {
                type: [String, Object, Function]
            },
            backToDefault: {
                type: [String, Object, Function]
            },
        },

        inheritAttrs: false,

        activated() {
            this.visible = true;
        },

        deactivated() {
            this.visible = false;
        },

        data() {
            return {
                visible: true
            }
        },

        beforeRouteEnter(from, to, next) {
            next(vm => vm.visible = true);
        },

        beforeRouteLeave() {
            this.visible = false;
        },

        methods: {
            /**
             * The visibility condition is required because when the user clicks on the router link
             * to another route in this modal, this function will be called
             * and without condition will be fulfilled this.$router.go(-1) from new route (called from link).
             */
            close() {
                if (this.visible) {

                    this.$emit('close');

                    if (this.backTo) {
                        return this.handleBackTo(this.backTo)
                    }

                    if (this.$routerHistory.hasPrevious()) {
                        return this.$router.replace(this.$routerHistory.previous())
                    }

                    if (this.backToDefault) {
                        return this.handleBackTo(this.backToDefault)
                    }

                }
            },

            handleBackTo(back_to) {
                let type = typeof back_to;

                if (type === "function") {
                    back_to();
                } else {
                    this.$router.replace(back_to);
                }
            },
        }

    }
</script>