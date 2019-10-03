jQuery(($) => {
    const msgField = document.querySelector('#notice_message');
    if (!!msgField && window.shortAndSweet) shortAndSweet(msgField, { counterClassName: 'help-block' });
});
