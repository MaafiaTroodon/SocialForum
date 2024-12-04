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
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
</div>