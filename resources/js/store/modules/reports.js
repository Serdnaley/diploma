import moment from 'moment';

const rand = (min = 0, max = 1, to_fixed = 0) => (Math.random() * (max - min) + min).toFixed(to_fixed);

let reports = [];

_.range(0, 30).map(i => {
    let date = rand(
        moment().startOf('year').unix(),
        moment().endOf('year').unix()
    );
    reports.push({
        id: rand(0, 10000),
        created_at: moment.unix(date).format('YYYY-MM-DD'),
        is_done: !!rand(0,1),
        user: {
            first_name: 'Павлюк',
            last_name: 'Андрей',
            patronymic: 'Игоревич',
            full_name: 'Павлюк Андрей Игоревич',
            short_name: 'Павлюк А.И.',
        },
        attachments: _.range(0, rand(1,5))
    });
});

export default {

    state: {
        reports,
    },

    getters: {
        reports: (state) => state.reports,

        reports_sorted_by_date: (state, getters) => {
            return [...getters.reports].sort((a,b) => moment(b.created_at) - moment(a.created_at))
        },

        reports_grouped_by_date: (state, getters) => {
            const result = [];
            let prev = false;

            getters.reports_sorted_by_date.map(report => {
                if (
                    !prev || (
                        moment(prev.created_at).format('YYYY-MM-DD')
                        !==
                        moment(report.created_at).format('YYYY-MM-DD')
                    )
                ) {
                    result.push({
                        created_at: report.created_at,
                        items: [
                            report
                        ],
                    });
                } else {
                    let last = result[result.length - 1];
                    last.items.push(report);
                }
                prev = report;
            });

            return result;
        },

        reports_grouped_by_month: (state, getters) => {
            const result = [];
            let prev = false;

            getters.reports_sorted_by_date.map(report => {
                if (
                    !prev || (
                        moment(prev.created_at).format('YYYY-MM')
                        !==
                        moment(report.created_at).format('YYYY-MM')
                    )
                ) {
                    result.push({
                        created_at: report.created_at,
                        items: [
                            report
                        ],
                    });
                } else {
                    let last = result[result.length - 1];
                    last.items.push(report);
                }
                prev = report;
            });

            return result;
        },
    },

    mutations: {},

    actions: {},

}