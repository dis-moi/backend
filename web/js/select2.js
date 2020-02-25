jQuery(() => {
  new MutationObserver(() => {
    jQuery('[data-widget=select2]:not(.select2-hidden-accessible)').select2()
  })
    .observe(document, {
      childList: true,
      subtree: true
    })
})
