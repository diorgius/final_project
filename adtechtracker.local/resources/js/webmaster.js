import Subscription from './modules/Subscription.js';
import OfferStatusListener from './modules/OfferStatusListener.js';
import OfferDeleteListener from './modules/OfferDeleteListener.js';

new Subscription('.offers__item', '.offers');
new OfferStatusListener('.active-offers', '.deactive-offers', '.subscriptions','.unsubscriptions');
new OfferDeleteListener();