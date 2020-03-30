<template>
    <el-row
        v-if="$auth.ready()"
        class="layout"
    >
        <el-aside class="aside">
            <div class="aside__profile">
                <i class="aside__profile-icon el-icon-user"/>
                <div>
                    {{ $auth.user().last_name }}
                    {{ $auth.user().first_name }}
                    <br>
                    {{ $auth.user().patronymic }}
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
                v-model="active_index"
            >
                <el-tab-pane
                    v-for="(item, index) in menu"
                    :key="index"
                    :label="item.name"
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
                menu: [
                    {
                        name: 'Цикловые комиссии',
                        route: {name: 'Categories'},
                    },
                    {
                        name: 'Пользователи',
                        route: {name: 'Users'},
                    },
                    {
                        name: 'Отчеты',
                        route: {name: 'Reports'},
                    },
                    {
                        name: 'Настройки',
                        route: {name: 'Settings'},
                    },
                ],
                active_index: 0,
            }
        },

        watch: {
            $route: {
                handler() {
                    this.handleRouteChange();
                },
                immediate: true,
            },

            active_index(index) {
                let item = this.menu[+index];
                if (item && item.route && this.$route.name !== item.route.name) {
                    this.$router.push(item.route);
                }
            },
        },

        methods: {
            handleRouteChange() {
                for (let index in this.menu) {

                    if (!this.menu.hasOwnProperty(index)) continue;

                    let needed = this.menu[index].route.name;
                    let reversedRoute = _.clone(this.$route.matched).reverse();

                    if (reversedRoute.some(({name}) => name && name === needed)) {
                        this.active_index = index;
                    }
                }
            },

            exit() {
                this
                    .$confirm('Вы действительно хотите выйти из системы?', {
                        confirmButtonText: 'Выйти',
                        cancelButtonText: 'Нет',
                    })
                    .then(() => this.$auth.logout())
                    .catch(_.noop)
            },
        },
    }
</script>

<style lang="scss">
    @import "../../scss/variables";

    .layout {
        display: flex;
        height: 100vh;
        padding-bottom: 100px;

        .aside {
            position: fixed;
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
            padding: 40px 30px 100px 380px;
            overflow: visible;
            min-height: 100vh;

            h1 {
                text-transform: uppercase;
                color: $--color-text-primary;
            }
        }
    }
</style>