<template>
    <route-modal
        :back-to-default="{name: 'Categories'}"
        :title="(category_id ? 'Редактировать' : 'Добавить') + ' комиссию'"
    >
        <el-form
            ref="form"
            :model="form"
            :rules="rules"
            v-loading="loading"
            @keyup.enter="submit()"
        >

            <el-form-item
                label="E-mail"
                prop="email"
            >
                <el-input
                    type="email"
                    name="email"
                    v-model="form.email"
                />
            </el-form-item>

        </el-form>
    </route-modal>
</template>

<script>
    import RouteModal from "../../components/RouteModal";
    import {mapGetters, mapActions} from 'vuex';

    export default {
        name: "AddEditCategory",

        components: {
            RouteModal,
        },

        data() {
            return {
                loading: false,

                form: {
                    first_name: '',
                    last_name: '',
                    patronymic: '',
                    role: '',
                    email: '',
                    category: '',
                },
                rules: {

                },
            }
        },

        computed: {
            ...mapGetters(['category']),

            category_id() {
                return this.$route.params.category_id;
            },
        },

        methods: {
            ...mapActions(['getCategory']),

            async fetchData() {
                this.loading = true;

                await this
                    .getCategory(this.category_clone)
                    .catch((err) => {
                        this.$message.error(
                            errorHandler(err).message
                            || 'Не удалось ' + (this.category_id ? 'отредактировать' : 'добавить') + ''
                        );
                    });

                this.loading = false;
            },
        },
    }
</script>