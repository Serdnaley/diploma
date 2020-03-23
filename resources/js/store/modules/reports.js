import moment from 'moment';

const rand = (min = 0, max = 1, to_fixed = 0) => (Math.random() * (max - min) + min).toFixed(to_fixed);

let items = [];

Array(30).map(i => {
    items.push({
        id: rand(0, 10000),
        date: moment(
            rand(
                moment().startOf('year'),
                moment().endOf('year')
            )
        ).format('YYYY-MM-DD'),
        user: {
            first_name: 'Павлюк',
            last_name: 'Андрей',
            patronymic: 'Игоревич',
            full_name: 'Павлюк Андрей Игоревич',
            short_name: 'Павлюк А.И.',
        }
    });
});

export default {

    state: {
        items,
    },

    getters: {
        items: state => state.items,
    },

    mutations: {},

    actions: {

    },

}