<!DOCTYPE html>
<html>
<head>
    <?php $this->load->view('assets/bootstrap') ?>
  <!-- VueJS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.16/vue.js"></script>
  <!-- SortableJS -->
  <script src="https://unpkg.com/sortablejs@1.4.2"></script>
  <!-- VueSortable -->
  <script src="https://unpkg.com/vue-sortable@0.1.3"></script>
</head>
<body>
    <ul v-sortable class="list-group">
      <li class="list-group-item">Foo</li>
      <li class="list-group-item">Foo 2</li>
      <li class="list-group-item">Foo 3</li>
    </ul>
    <script>
        new Vue({
          el: 'body'
        });
    </script>
</body>
</html>