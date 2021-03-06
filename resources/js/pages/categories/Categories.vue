<template>
    <div class="categories">

        <router-view/>

        <el-row
            type="flex"
            justify="space-between"
            align="middle"
            class="title"
        >
            <h1>Циклові комісії</h1>
            <el-button
                type="primary"
                @click="add()"
            >
                Створити комісію
            </el-button>
        </el-row>

        <div
            class="entities-list"
            v-loading="loading"
        >

            <el-card shadow="never" class="entities-list__head">
                <el-row>
                    <el-col :span="20">
                        Ім'я
                    </el-col>
                    <el-col :span="4">
                        Дії
                    </el-col>
                </el-row>
            </el-card>

            <div
                class="entities-list__group-items"
                v-if="categories && categories.length"
            >
                <div
                    class="entities-list__group-item"
                    v-for="category in categories"
                    :key="category.id"
                >
                    <el-card shadow="never">
                        <el-row>
                            <el-col :span="20">
                                {{ category.name || '-' }}
                            </el-col>
                            <el-col :span="4">
                                <span
                                    class="color-primary clickable"
                                    style="margin-right: 10px;"
                                    @click="edit(category)"
                                >
                                    Змінити
                                </span>
                                <span
                                    class="color-danger clickable"
                                    style="margin-right: 10px;"
                                    @click="doDelete(category)"
                                >
                                    Видалити
                                </span>
                            </el-col>
                        </el-row>
                    </el-card>
                </div>
            </div>

            <div
                class="entities-list__no-items"
                v-else
            >
                Нічого не знайдено
            </div>

        </div>
    </div>
</template>

<script>
    import {formatDate, formatNumber} from '../../util/filters';
    import {mapGetters, mapActions} from 'vuex';
    import {errorHandler} from "../../util";

    export default {
        name: "Users",

        filters: {
            formatDate,
            formatNumber,
        },

        data() {
            return {
                filter_data: {
                    active_tab: 'fluorography',
                    group_by: 'month',
                    date_range: [],
                },
                loading: false,
            }
        },

        beforeRouteEnter(to, from, next) {
            next(vm => vm.fetchData());
        },

        computed: {
            ...mapGetters(['categories']),
        },

        methods: {
            ...mapActions(['getCategories', "deleteCategory"]),

            async fetchData() {
                this.loading = true;

                await this.getCategories()
                    .catch((err) => {
                        this.$message.error(errorHandler(err).message || 'Не вдалося завантажити комісії')
                    });

                this.loading = false;
            },

            add() {
                this.$router.push({
                    name: 'AddCategory',
                });
            },

            edit(category) {
                this.$router.push({
                    name: 'EditCategory',
                    params: {category_id: category.id}
                });
            },

            async doDelete(item) {

                let confirm = await this
                    .$confirm(
                        'Ви дійсно хочете видалити "' + item.name + '"?',
                        'Підтвердіть дію',
                        {
                            confirmButtonText: 'Видалити',
                            cancelButtonText: 'Відміна',
                        }
                    )
                    .catch(_.noop);

                if (!confirm) return;

                this.loading = true;

                await this
                    .deleteCategory({id: item.id})
                    .catch(err => {
                        this.$message.error(errorHandler(err).message || 'Не вдалося видалити комісію');
                    });

                this.loading = false;
            },
        },
    }
</script>

<style lang="scss">
    @import '../../../scss/variables';

    .categories {

        .title {
            margin-bottom: 30px;
        }

        .header-divider {
            height: 30px;
            width: 1px;
            background: $--color-text-placeholder;
            margin: 0 30px;
        }

        .date-range.el-input__inner:not(:hover):not(:focus):not(.is-active) {
            border-color: transparent;
        }

    }
</style>
