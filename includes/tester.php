<?php



?>


<div class="modal-object modaltest" id="modal-test">asdf</div>


<script>

// const modalTest = new Modal('#modal-test');
// setTimeout(() => {
//     modalTest.show();    
// }, 1000);

// const modalTest = new ModalOpt('#modal-test');
// setTimeout(() => {
//     modalTest.show();    
// }, 1000);

const loading = new ModalLoading();
setTimeout(() => {
    loading.show();    
}, 1000);
setTimeout(() => {
    loading.hide();    
}, 4000);
    
</script>