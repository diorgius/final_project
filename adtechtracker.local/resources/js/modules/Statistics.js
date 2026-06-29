/**
 * Класс вывода статистики
 */
export default class Statistics {

    constructor(){
        this.buttons = document.querySelectorAll('.period-btn');
        this.activeButton = ['bg-indigo-600', 'text-white', 'border-indigo-600'];
        this.init();
    }

    // переключаем класс кнопки и получаем период
    init() {
        this.buttons.forEach(button => {
            button.addEventListener('click', () => {
                this.buttons.forEach(btn => {
                    btn.classList.remove(...this.activeButton);
                });
                button.classList.add(...this.activeButton);
                this.getStatistics(button.dataset.period);
            });
        });
    }

    // метод получения данных из БД в зависимости от даты
    async getStatistics(period) {
        const role = window.userRole;
        try {
            const response = await fetch(
                `/${role}/statistics/summary?period=${period}`, {
            });
            const data = await response.json();
            this.updateStatistics(period, data, role);
        } catch (error) {
            console.error(error);
        }
    }  

    // метод вывода полученных данных
    updateStatistics(period, data, role) {
        const dateTimeCurrent = new Date();
        let periodLang ='';
        switch (period) {
            case 'day':
                periodLang = __('day');
                break;
            case 'month':
                periodLang = __('month');
                break;
            case 'year':
                periodLang = __('year');
                break;
            case 'all':
                periodLang = __('alltime');
                break;
            default:
                periodLang = __('alltime');
                break;
        }

        document.getElementById('period-date').textContent = `${__('title_for_js')} ${periodLang} ${__('title_for_js_on')} ` 
            + dateTimeCurrent.toLocaleTimeString("ru-RU") + ' ' + dateTimeCurrent.toLocaleDateString("ru-RU");
        
        // если админ
        if (role === 'admin') {
            document.getElementById('advertisers').textContent = data.advertisers;
            document.getElementById('webmasters').textContent = data.webmasters;
            document.getElementById('offers').textContent = data.offers;
            document.getElementById('deleted-offers').textContent = data.deletedOffers;
            document.getElementById('subscriptions').textContent = data.subscriptions;
            document.getElementById('active-subscriptions').textContent = data.activeSubscriptions;
            document.getElementById('deactive-subscriptions').textContent = data.deactiveSubscriptions;
            document.getElementById('clicks').textContent = data.clicks;
            document.getElementById('rejected-clicks').textContent = data.rejectedClicks;
            document.getElementById('advertiser-expenses').textContent = Number(data.advertiserExpenses).toFixed(2);
            document.getElementById('webmaster-income').textContent = Number(data.webmasterIncome).toFixed(2);
            document.getElementById('system-profit').textContent = Number(data.systemProfit).toFixed(2);
        }

        // если рекламодатель или вебмастер
        if (role === 'advertiser' || role === 'webmaster') {
            const tbody = document.getElementById('offers-table-body');
            tbody.innerHTML = '';
            data.offers.forEach(offer => {
                tbody.insertAdjacentHTML(
                    'beforeend',
                    `<tr class="border-b border-gray-200 dark:text-gray-200 text-xl">
                        <td class="py-2">${offer.name}</td>
                        <td class="py-2">${offer.deleted_at === null ? __('active') : __('deleted')}</td>
                        <td class="py-2">${offer.click_count}</td>
                        <td class="py-2">${role === 'advertiser' ?
                            Number(offer.advertiser_expenses).toFixed(2) :
                            Number(offer.webmaster_revenue).toFixed(2)
                            }</td>
                    </tr>`
                );
            });
            document.getElementById('total-clicks').textContent = data.totalClicks;
            role === 'advertiser' ?
            document.getElementById('total-expenses').textContent = Number(data.totalExpenses).toFixed(2) :
            document.getElementById('total-expenses').textContent = Number(data.totalRevenue).toFixed(2);
        }
    }
}

// new Statistics();