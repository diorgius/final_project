/**
 * Класс вывода статистики
 */
class Statistics {

    constructor(){
        this.init();
    }

    // получаем период
    init(){
        document.querySelectorAll('input[name="period"]')
        .forEach(radio => {
            radio.addEventListener('change', async e => {
                const period = e.target.value;
                this.getStatistics(period);
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
        console.log(data);
        const dateTimeCurrent = new Date();

        let periodRus ='';
        
        switch (period) {
            case 'day':
                periodRus = 'день'
                break;
            case 'month':
                periodRus = 'месяц'
                break;
            case 'year':
                periodRus = 'год'
                break;
            case 'all':
                periodRus = 'все время'
                break;
            default:
                periodRus = 'все время'
                break;
        }

        document.getElementById('period-date').innerText = `Статистика за ${periodRus} на ` 
            + dateTimeCurrent.toLocaleTimeString("ru-RU") + ' ' + dateTimeCurrent.toLocaleDateString("ru-RU");
        
        // если админ
        if (role === 'admin') {
            document.getElementById('advertisers').textContent = data.advertisers;
            document.getElementById('webmasters').textContent = data.webmasters;
            document.getElementById('offers').textContent = data.offers;
            document.getElementById('subscriptions').textContent = data.subscriptions;
            document.getElementById('unsubscriptions').textContent = data.unsubscriptions;
            document.getElementById('clicks').textContent = data.clicks;
            document.getElementById('rejected-clicks').textContent = data.rejectedClicks;
            document.getElementById('advertiser-expenses').textContent = data.advertiserExpenses;
            document.getElementById('webmaster-income').textContent = data.webmasterIncome;
            document.getElementById('system-profit').textContent = data.systemProfit;
        }

        // если рекламодатель
        if (role === 'advertiser') {
            console.log(data);
            const tbody = document.getElementById('offers-table-body');

            tbody.innerHTML = '';

            data.offers.forEach(offer => {
                tbody.insertAdjacentHTML(
                    'beforeend',
                    `<tr class="border-b border-gray-200 text-xl">
                        <td class="py-2">${offer.name}</td>
                        <td class="py-2">${offer.click_count}</td>
                        <td class="py-2">${Number(offer.advertiser_expenses).toFixed(2)}</td>
                    </tr>`
                );
            });
            document.getElementById('total-clicks').textContent = data.totalClicks;
            document.getElementById('total-expenses').textContent = data.totalExpenses;
        }

        // если вебмастер
        if (role === 'webmaster') {

        }
    }
}

new Statistics();