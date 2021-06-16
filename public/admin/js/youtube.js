$('.video-preview-link').on('click', function (e) {
  e.preventDefault();
  const modal = $($(this).attr('href'));
  modal.find('iframe').attr('src', 'https://www.youtube.com/embed/' + $(this).data('video'));
  modal.modal('show');
});

$('.video-preview-close').on('click', function () {
  const modal = $(this).closest('.modal');
  modal.find('iframe').attr('src', '');
  modal.modal('hide');
});