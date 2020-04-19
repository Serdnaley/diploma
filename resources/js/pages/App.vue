<template>
    <el-row
        v-if="$auth.ready()"
        class="layout"
    >
        <el-aside class="aside">
            <div class="aside__profile">
                <div class="aside__profile-user">
                    <i class="aside__profile-icon el-icon-user"/>
                    <div>
                        {{ $auth.user().last_name }}
                        {{ $auth.user().first_name }}
                        <br>
                        {{ $auth.user().patronymic }}
                    </div>
                </div>
                <div class="aside__profile-actions">
                    <el-button
                        type="text"
                        class="aside__profile-exit"
                        @click="exit()"
                    >
                        выйти
                    </el-button>
                    <el-button
                        type="link"
                        size="mini"
                        plain
                        class="aside__profile-menu-toggle"
                        @click="view_mobile_menu = !view_mobile_menu"
                    >
                        меню
                    </el-button>
                </div>
            </div>
            <el-collapse-transition>
                <div v-show="view_mobile_menu">
                    <el-divider/>
                    <el-tabs
                        tab-position="left"
                        class="aside__menu"
                        :value="active_index"
                        @tab-click="handleChange"
                    >
                        <el-tab-pane
                            v-for="(item, index) in menu"
                            :key="index"
                            :label="item.name"
                        />
                    </el-tabs>
                    <div class="aside__footer">
                        <div class="heart">
                            <template v-for="i in 5">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" enable-background="new 0 0 64 64">
                                    <path d="m61.07 18.16c-6.395-16.918-27.15-9.328-29.07-.879-2.64-9-22.89-15.721-29.07.891-6.881 18.502 26.67 35.11 29.07 37.828 2.397-2.162 35.952-19.639 29.07-37.84"/>
                                </svg>
                            </template>
                        </div>
                        <div class="text">
                            Дипломный проект
                            <br>
                            Студента 452 группы
                            <br>
                            Павлюка Андреса
                        </div>
                    </div>
                </div>
            </el-collapse-transition>
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
                view_mobile_menu: innerWidth > 980,
            }
        },

        computed: {
            menu() {
                const menu = [];

                if (this.$auth.check(['admin', 'manager', 'user'])) {
                    menu.push({
                        name: 'Личный кабинет',
                        route: 'Account',
                    });
                }

                if (this.$auth.check(['admin', 'manager'])) {
                    menu.push({
                        name: 'Все отчеты',
                        route: 'Reports',
                    });

                    menu.push({
                        name: 'Пользователи',
                        route: 'Users',
                    });

                    menu.push({
                        name: 'Цикловые комиссии',
                        route: 'Categories',
                    });
                }

                if (this.$auth.check(['admin'])) {
                    menu.push({
                        name: 'Настройки',
                        route: 'Settings',
                    });
                }

                return menu;
            },

            active_index() {
                return _.findIndex(this.menu, {route: this.$route.name}).toString();
            },
        },

        methods: {
            handleChange(tab) {
                this.view_mobile_menu = innerWidth > 980;
                let item = this.menu[tab.index];
                if (item && item.route !== this.$route.name)
                    this.$router.push({name: item.route});
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
            padding-bottom: 100px;

            &__profile {
                position: relative;
                display: flex;
                align-items: center;

                padding: 50px 30px 30px 30px;
                color: $--color-primary;

                &-user {
                    display: flex;
                    align-items: center;
                }

                &-icon {
                    font-size: 50px;
                    margin-right: 15px;
                    margin-bottom: 5px;
                }

                &-actions {
                    display: inline-block;
                    position: absolute;
                    top: 0;
                    right: 10px;
                }

                &-exit {
                    color: $--color-text-placeholder;
                }

                &-menu-toggle {
                    display: none;
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

            &__footer {
                position: fixed;
                bottom: 0;
                height: 100px;
                width: 300px;
                z-index: 2;

                display: flex;
                align-items: center;

                padding: 0 30px;
                box-sizing: border-box;

                background: $--color-white;

                cursor: default;
                user-select: none;

                $transition: 3s;

                .text {
                    margin-left: -30px;
                    transition: $transition;
                }

                .heart {
                    position: relative;
                    display: inline-block;
                    height: 30px;
                    width: 30px;
                    vertical-align: top;
                    transform: scale(2);

                    svg {
                        position: absolute;
                        height: 100%;
                        width: 100%;
                        animation: heart-pulse 10s infinite ease-in-out;

                        @for $n from 1 to 5 {
                            &:nth-child(#{$n}) {
                                animation-delay: #{$n*2}s;
                            }
                        }

                        path {
                            fill: transparent;
                            transition: $transition;
                        }
                    }
                }

                &:hover {
                    .text {
                        margin-left: 15px;
                    }

                    .heart {
                        svg {
                            path {
                                fill: #f46767;
                            }
                        }
                    }
                }
            }
        }

        .main {
            padding: 40px 30px 100px 380px;
            overflow: visible;
            min-height: 100vh;

            h1 {
                color: $--color-text-primary;
            }
        }

        @media (max-width: 980px) {
            flex-direction: column;

            .aside {
                position: relative;
                padding: 0;
                width: 100% !important;
                height: auto;

                &__profile {
                    padding: 10px 20px 10px 20px;
                    justify-content: space-between;

                    &-actions {
                        position: relative;
                    }

                    &-menu-toggle {
                        display: inline-block;
                    }
                }

                &__footer {
                    display: none;
                }
            }

            .main {
                padding-left: 30px;
            }
        }
    }

    @keyframes heart-pulse {
        0% {
            transform: scale(0);
            opacity: 0.8;
        }
        80% {
            transform: scale(1.2);
            opacity: 0;
        }
        100% {
            transform: scale(2.2);
            opacity: 0;
        }
    }
</style>