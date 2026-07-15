import Subscription from './modules/Subscription.js';
import OfferStatusForWebmasterListener from './modules/OfferStatusForWebmasterListener.js';
import OfferDeleteListener from './modules/OfferDeleteListener.js';

new Subscription('.offers__item', '.offers');
new OfferStatusForWebmasterListener('.subscriptions','.unsubscriptions');
new OfferDeleteListener();