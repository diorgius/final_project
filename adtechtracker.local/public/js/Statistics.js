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
            this.updateStatistics(data, role);
        } catch (error) {
            console.error(error);
        }
    }  

    // метод вывода полученных данных
    updateStatistics(data, role) {
        console.log(data);
        if (role === 'admin') {
            document.getElementById('advertisers').innerText = data.advertisers;
            document.getElementById('webmasters').innerText = data.webmasters;
            document.getElementById('offers').innerText = data.offers;
            document.getElementById('subscriptions').innerText = data.subscriptions;
            document.getElementById('unsubscriptions').innerText = data.unsubscriptions;
            document.getElementById('clicks').innerText = data.clicks;
            document.getElementById('rejectedClicks').innerText = data.rejectedClicks;
            document.getElementById('advertiserExpenses').innerText = data.advertiserExpenses;
            document.getElementById('webmasterIncome').innerText = data.webmasterIncome;
            document.getElementById('systemProfit').innerText = data.systemProfit;
        }

        if (role === 'advertiser') {

        }

        if (role === 'webmaster') {

        }
    }
}

new Statistics();