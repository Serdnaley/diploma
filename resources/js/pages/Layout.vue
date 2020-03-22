<template>
    <el-row class="layout">
        <el-aside class="aside">
            <div class="aside__profile">
                <i class="aside__profile-icon el-icon-user-solid"/>
                <div>
                    Карпенко Томара
                    <br>Ивановна
                </div>
            </div>
            <div class="aside__menu">
                <div class="aside__menu-item">
                    Настройки
                </div>
                <div class="aside__menu-item">
                    Выйти
                </div>
            </div>
        </el-aside>
        <el-main>
            <router-view/>
        </el-main>
    </el-row>
</template>

<script>
    export default {
        name: "Layout",

        data() {
            return {
            }
        },
    }
</script>

<style lang="scss">
    @import "../../sass/variables";
    @import "~element-ui/packages/theme-chalk/src";
    @import "~element-ui/lib/theme-chalk/display.css";
    @import "~@fortawesome/fontawesome-free/scss/fontawesome";

    body {
        margin: 0;
        font-family: Roboto, sans-serif;
    }

    .layout {
        display: flex;
        height: 100vh;

        .aside {
            background: #F2F2F2;
            height: 100%;

            &__profile {
                display: flex;
                align-items: center;

                padding: 50px 30px 30px 30px;
                background: $--color-primary;
                color: $--color-white;

                &-icon {
                    font-size: 50px;
                    margin-right: 15px;
                    margin-bottom: 5px;
                }
            }

            &__menu {
                padding: 30px 0;

                &-item {
                    padding: 10px 30px;
                    cursor: pointer;
                    transition: .1s;

                    &:hover {
                        background: transparentize($--color-black, .9);
                    }
                }
            }
        }
    }
</style>