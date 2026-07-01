/**
 * Класс проверки ранее созданных офферов и вывода модального окна для выбора действия
 */
export default class OfferCheck {

    constructor() {

        this.offerCreateForm = document.querySelector('#offer-create');

        if (!this.offerCreateForm) {
            return;
        }

        this.offerCreateForm.addEventListener(
            'submit',
            this.submit.bind(this)
        );

    }

    async submit(event) {

        event.preventDefault();

        try {
            const data = await this.checkOffer(
                new FormData(this.offerCreateForm)
            );
            
            if (data === null) {
                return;
            }

            if (!data.offer) {

                this.offerCreateForm.submit();

                return;

            }
            
        } catch (error) {
            console.error(error);
        }

    }

    async checkOffer(formData) {

        const response = await fetch('/advertiser/offers/check', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });

        const data = await response.json();

        if (!response.ok) {

            console.log(data);
            this.showErrors(data.errors);

            return null;

        }

        return data;
    }

    showRestoreModal(data) {
            
    }

    createNewOffer() {
            
    }

    restoreOffer(id) {

    }

    showErrors(errors) {

        Object.keys(errors).forEach(field => {

            const errorBlock = document.getElementById(`${field}-error`);

            if (!errorBlock) {
                return;
            }

            errorBlock.innerHTML = '';

            errors[field].forEach(message => {

                errorBlock.innerHTML += `
                    <p class="text-sm text-red-600 dark:text-red-400">
                        ${message}
                    </p>
                `;

            });

        });

    }
}