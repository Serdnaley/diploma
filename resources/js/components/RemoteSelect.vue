<template>
    <el-select

        v-bind="{

            remoteMethod,
            loading,
            reverseKeywords: true,
            remote: true,
            filterable: true,
            defaultFirstOption: true,
            valueKey: 'id',

            ...$attrs
        }"

        :value="options ? $attrs.value : defaultValue"

        v-on="{

            input: ($event) => $emit('input', $event),

            ...$listeners
        }"
    >
        <slot/>
        <slot
            name="item"
            v-for="item in options"
            :item="item"
        />
    </el-select>
</template>

<script>
    export default {
        name: "RemoteSelect",

        inheritAttrs: false,

        props: {
            method: {
                type: Function,
                required: true,
            },
            dataLocation: {
                type: String,
                default: 'data.data',
            },
            autoExecuteMethod: {
                type: Boolean,
                default: true,
            },
            catch: {
                type: [Function, String],
                default: 'Не удалось загрузить значения для выбора.'
            },
            defaultValue: {
                type: [String, Object, Number]
            }
        },

        data() {
            return {
                options: false,
                loading: false,
            }
        },

        mounted() {
            if (this.autoExecuteMethod) {
                this.remoteMethod();
            }
        },

        methods: {

            async remoteMethod(search = '') {
                this.loading = true;

                let res = await this
                    .method({search, per_page: 300 })
                    .catch(
                        typeof this.catch === 'function'
                            ? this.catch
                            : () => this.$message.error(this.catch || 'Не удалось загрузить элементы списка')
                    );

                if (res) {
                    let data = _.at(res, this.dataLocation)[0];

                    if (!data) {
                        throw Error(`Remote Select: Path '${this.dataLocation}' is not found in the method's result`)
                    }

                    this.options = data;
                }

                this.loading = false;
            },
        },
    }
</script>
