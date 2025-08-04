export const __ = window.wp?.i18n?.__ || ((s) => s);
export const _x = window.wp?.i18n?._x || ((s) => s);
export const _n = window.wp?.i18n?._n || ((s, p, n) => (n === 1 ? s : p));
export const sprintf = window.wp?.i18n?.sprintf || ((...args) => args.join(' '));
