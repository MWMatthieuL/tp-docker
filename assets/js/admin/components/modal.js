function centerModal() {
  $(this).css('display', 'block');
  const $dialog = $(this).find('.modal-dialog');
  let offset = ($(window).height() - $dialog.height()) / 2;
  const bottomMargin = parseInt($dialog.css('marginBottom'), 10);
  if (offset < bottomMargin) offset = bottomMargin;
  $dialog.css('margin-top', offset);
}

jQuery(document).ready(($) => {
  //= = Center Modal
  $(document).on('show.bs.modal', '.modal', centerModal);
});
