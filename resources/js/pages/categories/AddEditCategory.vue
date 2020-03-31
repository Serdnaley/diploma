<template>
    <route-modal
        :back-to-default="{name: 'Categories'}"
        :title="(category_id ? 'Редактировать' : 'Добавить') + ' комиссию'"
        width="30%"
        @close="handleClose()"
        ref="modal"
    >
        <el-form
            ref="form"
            :model="category_clone"
            :rules="rules"
            v-loading="loading"
            @keyup.enter="submit()"
        >

            <el-form-item
                label="Название:"
                prop="name"
            >
                <el-input v-model="category_clone.name"/>
            </el-form-item>

            <ul v-if="errors" class="color-danger">
                <li v-for="error in errors">
                    {{ error[0] }}
                </li>
            </ul>

            <el-form-item>
                <el-button
                    type="primary"
                    @click="submit()"
                >
                    {{ category_id ? 'Сохранить' : 'Добавить' }}
                </el-button>
            </el-form-item>

        </el-form>
    </route-modal>
</template>

<script>
    import RouteModal from "../../components/RouteModal";
    import {mapGetters, mapActions} from 'vuex';
    import {validateForm, resetForm, errorHandler} from "../../util";

    export default {
        name: "AddEditCategory",

        components: {
            RouteModal,
        },

        data() {
            return {
                loading: false,

                rules: {
                    name: [
                        {
                            required: true,
                            message: 'Поле обязательно к заполнению',
                            trigger: 'blur',
                        },
                        {
                            min: 3,
                            message: 'Слишком короткое название',
                            trigger: 'blur',
                        },
                    ],
                },

                category_default: {
                    name: '',
                },

                category_clone: {},
            }
        },

        beforeRouteEnter(to, from, next) {
            next(vm => vm.fetchData());
        },

        beforeRouteUpdate(to, from, next) {
            this.$nextTick(() => this.fetchData());
            next()
        },

        computed: {
            ...mapGetters(['category']),

            category_id() {
                return this.$route.params.category_id;
            },
        },

        watch: {
            category() {
                this.category_clone = _.cloneDeep(this.category);
            }
        },

        methods: {
            ...mapActions(['getCategory', "updateCategory", "addCategory"]),

            async fetchData() {
                this.loading = true;

                if (this.category_id) {
                    await this
                        .getCategory({id: this.category_id})
                        .catch((err) => {
                            this.$message.error(
                                errorHandler(err).message
                                || 'Не удалось загрузить комиссию'
                            );
                        });
                } else {
                    this.category_clone = _.cloneDeep(this.category_default);
                }

                this.loading = false;
            },

            async submit() {

                if (!validateForm(this.$refs.form)) {
                    this.$notify({
                        title: 'Данные введены неверно',
                        type: 'error',
                        position: 'bottom-left',
                    });
                    return false;
                }

                this.loading = true;

                let method = this.category_id
                    ? this.updateCategory
                    : this.addCategory;

                let res = await method(this.category_clone)
                    .then(() => {
                        this.category_clone = _.cloneDeep(this.category_default);
                        this.close();
                    })
                    .catch((err) => {
                        let error = errorHandler(err);
                        this.errors = error;
                        this.$message.error(error.message || 'Не удалось сохранить комиссию');
                    });

                this.loading = false;

                return res;
            },

            close() {
                this.$refs.modal.close();
            },

            handleClose() {
                resetForm(this.$refs.form);
            },
        },
    }
</script>