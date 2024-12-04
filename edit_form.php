<!-- edit_form.php -->
<div id="editModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Edit Post</h2>
        <form id="editPostForm">
            <input type="hidden" id="editPostId">
            <label for="editTitle">Title:</label>
            <input type="text" id="editTitle" required>
            <label for="editContent">Content:</label>
            <textarea id="editContent" required></textarea>
         
            <button class="btn btn-success" type="submit">
      
                <span>Update</span>
                <i class="fas fa-arrow-right"></i>
          
            </button>
        </form>
    </div>
</div>