let scrollingTimeout;
window.addEventListener('scroll', () => {
  const scrollMessage = document.querySelectorAll('.header')
  const scrollPosition = window.scrollY || document.documentElement.scrollTop
  document.body.classList.add('scrolling');
  clearTimeout(scrollingTimeout);
  scrollingTimeout = setTimeout(() => {
    document.body.classList.remove('scrolling');
  }, 1000);
  if (scrollPosition > 50) {
    scrollMessage.forEach(item => {
      item.classList.add('top-0')
      item.classList.replace('position-relative', 'position-fixed')
    })
  } else {
    scrollMessage.forEach(item => {
      item.classList.remove('top-0')
      item.classList.replace('position-fixed', 'position-relative')
    })
  }
})
