<template>
    <route-modal
        :back-to-default="{name: 'Categories'}"
        :title="(category_id ? 'Редагувати' : 'Створити') + ' комісію'"
        class="add-edit-category"
        width="800px"
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
                label="Назва:"
                prop="name"
            >
                <el-input v-model="category_clone.name"/>
            </el-form-item>

            <el-transfer
                filterable
                filter-placeholder="Пошук..."
                :titles="['Інші комісії', 'Ця комісія']"
                v-loading="users_loader"
                v-model="category_clone.user_ids"
                :data="users_data"
            />

            <ul v-if="errors" class="color-danger">
                <li v-for="error in errors">
                    {{ error[0] }}
                </li>
            </ul>

            <el-form-item>
                <el-button
                    type="primary"
                    @click="submit()"
                    style="margin-top: 30px;"
                >
                    {{ category_id ? 'Зберегти' : 'Створити' }}
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
                users_loader: false,

                errors: false,

                rules: {
                    name: [
                        {
                            required: true,
                            message: 'Поле обов\'язково до заповнення',
                            trigger: 'blur',
                        },
                        {
                            min: 3,
                            message: 'Занадто коротка назва',
                            trigger: 'blur',
                        },
                    ],
                },

                category_default: {
                    name: '',
                    user_ids: [],
                    users: [],
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
            ...mapGetters(['category', "users"]),

            category_id() {
                return this.$route.params.category_id;
            },

            users_data() {
                if (!this.users)
                    return [];
                else
                    return this.users.map(user => ({
                        key: user.id,
                        label: user.full_name,
                    }));
            },
        },

        watch: {
            category() {
                this.category_clone = _.cloneDeep(this.category);
                this.$set(this.category_clone, 'user_ids', []);
                this.category_clone.users.map(user => {
                    this.category_clone.user_ids.push(user.id);
                });
            }
        },

        methods: {
            ...mapActions(['getCategory', "updateCategory", "addCategory", "getUsers"]),

            async fetchData() {
                this.loading = true;

                if (this.category_id) {
                    await this
                        .getCategory({id: this.category_id})
                        .catch((err) => {
                            this.$message.error(
                                errorHandler(err).message
                                || 'Не вдалося завантажити комісію'
                            );
                        });
                } else {
                    this.category_clone = _.cloneDeep(this.category_default);
                }

                this.loading = false;

                await this.fetchUsers();
            },

            async fetchUsers() {
                this.users_loader = true;

                await this.getUsers();

                this.users_loader = false;
            },

            async submit() {

                if (!validateForm(this.$refs.form)) {
                    this.$notify({
                        title: 'Невірно введені дані',
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
                        this.$message.error(error.message || 'Не вдалося зберегти комісію');
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

<style lang="scss">
    .add-edit-category {
        .el-transfer {
            &-panel {
                width: 270px;
            }
        }
    }
</style>
