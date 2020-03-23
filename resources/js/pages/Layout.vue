<template>
    <el-row class="layout">
        <el-aside class="aside">
            <div class="aside__profile">
                <i class="aside__profile-icon el-icon-user"/>
                <div>
                    Карпенко Томара
                    <br>Ивановна
                </div>
                <el-button
                    type="text"
                    class="aside__profile-exit"
                    @click="exit()"
                >
                    выйти
                </el-button>
            </div>
            <el-divider/>
            <el-tabs
                tab-position="left"
                class="aside__menu"
            >
                <el-tab-pane
                    label="Пользователи"
                />
                <el-tab-pane
                    label="Добавить"
                />
                <el-tab-pane
                    label="Настройки"
                />
            </el-tabs>
        </el-aside>
        <el-main class="main">
            <router-view/>
        </el-main>
    </el-row>
</template>

<script>
    export default {
        name: "Layout",

        data() {
            return {
                data: [

                ],
            }
        },

        methods: {
            exit() {
                this.$confirm('Вы действительно хотите выйти из системы?', {
                    confirmButtonText: 'Выйти',
                    cancelButtonText: 'Нет',
                });
            }
        },
    }
</script>

<style lang="scss">
    @import "../../scss/variables";

    .layout {
        display: flex;
        height: 100vh;

        .aside {
            height: 100%;
            box-shadow: $--box-shadow-dark;

            &__profile {
                position: relative;
                display: flex;
                align-items: center;

                padding: 50px 30px 30px 30px;
                color: $--color-primary;

                &-icon {
                    font-size: 50px;
                    margin-right: 15px;
                    margin-bottom: 5px;
                }

                &-exit {
                    display: inline-block;
                    position: absolute;
                    top: 0;
                    right: 10px;
                    color: $--color-text-placeholder;
                }
            }

            &__menu {
                padding: 30px 0;

                .el-tabs {

                    &__nav-wrap {
                        margin: 0 !important;

                        &::after {
                            display: none !important;
                        }
                    }

                    &__header {
                        width: 100% !important;
                        margin-right: 0 !important;
                    }

                    &__item {
                        text-align: left !important;
                        font-size: $--font-size-medium !important;
                        padding: 0 30px !important;
                    }

                    &__active-bar {
                        width: 4px !important;
                        border-radius: 4px 0 0 4px !important;
                    }
                }
            }
        }

        .main {
            padding: 40px 30px 100px 80px;

            h1 {
                text-transform: uppercase;
                color: $--color-text-primary;
            }
        }
    }
</style>