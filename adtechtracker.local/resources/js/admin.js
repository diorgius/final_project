import Status from './modules/Status.js';
import OfferStatusListener from './modules/OfferStatusListener.js';
import OfferCreateListener from './modules/OfferCreateListener.js';
import OfferDeleteListener from './modules/OfferDeleteListener.js';
import OfferSubscribeListener from './modules/OfferSubscribeListener.js';

new Status('.offers__item', '.offers');
new OfferStatusListener('.active-offers', '.deactive-offers');
new OfferCreateListener('.active-offers', '.deactive-offers');
new OfferDeleteListener();
new OfferSubscribeListener();
