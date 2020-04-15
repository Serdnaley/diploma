import axios from 'axios';
import moment from 'moment';
import {queryString} from "../../util";

export default {

    state: {
        reports: [],
        report: null,
    },

    getters: {

        report: (state) => state.report,

        reports: (state) => {
            let result = [];
            let used_dates = {};
            let last_year = moment().subtract(1, 'year');
            let now = moment();

            state.reports.map(user => {
                let reports = [...user.reports];
                if (reports) {
                    reports
                        .sort((a, b) => moment(b.date) - moment(a.date))
                        .map(report => {

                            report = _.clone(report);

                            report.user = _.cloneDeep(user);

                            if (!report.id) {
                                if (!report.date) {
                                    report.status = 'new'
                                } else if (report.real_date && moment(report.real_date) < last_year && moment(report.date) < now) {
                                    report.term = moment(report.real_date).add(1, 'year').toISOString();
                                    report.status = 'expired';

                                    // "2019-02-15T00:00:00.000000Z"
                                    // "2020-02-15T02:00:00. 000+02:00"
                                } else {
                                    report.status = 'planned'
                                }
                            } else {
                                report.status = 'done'
                            }

                            if (report.id && used_dates.hasOwnProperty(report.id)) {

                                if (report.status === 'expired') {
                                    let existed_report = _.find(result, {id: report.id});
                                    existed_report.status = 'expired';
                                }

                                return false;
                            }

                            used_dates[report.id] = moment(report.date).format('YYYY-MM-DD');
                            result.push(report);
                        })
                }
            });

            return result;
        },

        reports_sorted_by_date: (state, getters) => {
            return [...getters.reports].sort((a, b) => moment(b.date) - moment(a.date))
        },

        reports_grouped_by_month: (state, getters) => {
            const result = [];
            let prev = false;

            getters.reports_sorted_by_date.map(report => {
                if (
                    !prev || (
                        moment(prev.date || 0).format('YYYY-MM')
                        !==
                        moment(report.date || 0).format('YYYY-MM')
                    )
                ) {
                    result.push({
                        date: report.date,
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

    mutations: {
        get_reports: (state, data) => state.reports = data,
        add_report: (state, data) => {
            state.report = data;
            state.reports.push(data);
        },
        get_report: (state, data) => state.report = data,
        update_report: (state, data) => {
            if (state.report.id === data.id) state.report = data;
            let index = _.findIndex(state.reports, {id: data.id});
            if (index !== -1) state.reports[index] = data;
        },
        delete_report: (state, data) => {
            if (state.report.id === data.id) state.report = null;
            let index = _.findIndex(state.reports, {id: data.id});
            if (index !== -1) state.reports.splice(index, 1);
        },
    },

    actions: {

        async getReports({commit}, data = {}) {
            let res = await axios.get(`report?${queryString(data)}`);

            commit('get_reports', res.data);

            return res;
        },

        async addReport({commit}, data = {}) {
            let res = await axios.post(`report`, data);

            commit('add_report', res.data);

            return res;
        },

        async getReport({commit}, data = {}) {
            let res = await axios.get(`report/${data.id}`);

            commit('get_report', res.data);

            return res;
        },

        async updateReport({commit}, data = {}) {
            let res = await axios.put(`report/${data.id}`, data);

            commit('update_report', res.data);

            return res;
        },

        async deleteReport({commit}, data) {
            let res = await axios.delete(`report/${data.id}`);

            commit('delete_report', data);

            return res;
        },

    },

}