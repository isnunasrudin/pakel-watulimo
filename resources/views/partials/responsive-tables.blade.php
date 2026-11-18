@pushOnce('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.article-body table').forEach((table) => {
    if (table.parentElement?.classList.contains('article-table-wrap')) return;

    const wrapper = document.createElement('div');
    wrapper.className = 'article-table-wrap';
    wrapper.setAttribute('role', 'region');
    wrapper.setAttribute('aria-label', 'Tabel dapat digeser ke samping untuk melihat seluruh isi');
    wrapper.setAttribute('tabindex', '0');

    table.parentNode.insertBefore(wrapper, table);
    wrapper.appendChild(table);
  });
});
</script>
@endPushOnce
