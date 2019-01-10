// Object Oriented Programing 101. Update and Delete 'My Notes'
import $ from "jquery";

// first create a class
class MyNotes {
  //second constructor
  constructor() {
    this.events();
  }

  //third events
  events() {
    //delete note button. hook events to parent 'my-notes' element to deal with future notes being added that don't yet exist
    $("#my-notes").on("click", ".delete-note", this.deleteNote);
    //edit note button
    $("#my-notes").on("click", ".edit-note", this.editNote.bind(this));
    // update note - blue save button
    $("#my-notes").on("click", ".update-note", this.updateNote.bind(this));
    //create note - submit form button
    $(".submit-note").on("click", this.createNote.bind(this));
  }

  //fourth define methods
  deleteNote(e) {
    // get the parent li of the item being clicked on. parent li contains ID from data-id on front end
    var thisNote = $(e.target).parents("li");
    //ajax tell the server which type of request you would like to make
    $.ajax({
      beforeSend: xhr => {
        // nonce = number used once. Provided by WP from 'wp_localize_scripts' in functions.php. Used to confirm user is the owner of the note
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url:
        // .data() is a jquery function for grabbing data attributes. data attribute is provided on 'page-my-notes'
        universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.data("id"),
      type: "DELETE",
      // if successful, run this function
      success: response => {
        thisNote.slideUp();
        console.log("congrats");
        console.log(response);
        // after deleting note, if the user now has less than 5 notes ..
        if (response.userNoteCount < 5) {
          // remove the warning message
          $(".note-limit-message").removeClass("active");
        }
      },
      error: response => {
        console.log("sorry");
        console.log(response);
      }
    });
  }

  createNote(e) {
    //data getting sent to backend
    var ourNewPost = {
      // what fields we are creating: where the value can be found on the ui
      title: $(".new-note-title").val(),
      content: $(".new-note-body").val(),
      status: "publish"
    };
    //ajax tell the server which type of request you would like to make
    $.ajax({
      beforeSend: xhr => {
        // nonce = number used once. Provided by WP from 'wp_localize_scripts' in functions.php. Used to confirm user is the owner of the note
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url: universityData.root_url + "/wp-json/wp/v2/note/",
      // what type of request are we making
      type: "POST",
      //object created above
      data: ourNewPost,
      // if successful, run this function
      success: response => {
        //reset form fields after submission
        $(".new-note-title, .new-note-body").val("");
        //add new post to li. #my-notes is the id of parent ul element
        $(`
          <li data-id="${response.id}">
            <input readonly class="note-title-field" value="${
              response.title.raw
            }">
            <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i>Edit</span>
            <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i>Delete</span>
            <textarea readonly class="note-body-field">${
              response.content.raw
            }</textarea>
            <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i>Save</span>
          </li>
        `)
          .prependTo("#my-notes")
          .hide()
          .slideDown();
        console.log("congrats");
        console.log(response);
      },
      error: response => {
        if (response.responseText == "You have reached your note limit.") {
          $(".note-limit-message").addClass("active");
        }
        console.log("sorry");
        console.log(response);
      }
    });
  }

  updateNote(e) {
    // get the parent li of the item being clicked on. parent li contains ID from data-id on front end
    var thisNote = $(e.target).parents("li");
    //data getting sent to backend
    var ourUpdatedPost = {
      // must match WP api keyword fields. find the class 'note-title-field' on ui and use that value
      title: thisNote.find(".note-title-field").val(),
      content: thisNote.find(".note-body-field").val()
    };
    //ajax tell the server which type of request you would like to make
    $.ajax({
      beforeSend: xhr => {
        // nonce = number used once. Provided by WP from 'wp_localize_scripts' in functions.php. Used to confirm user is the owner of the note
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url:
        // .data() is a jquery function for grabbing data attributes. data attribute is provided on 'page-my-notes'
        universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.data("id"),
      type: "POST",
      data: ourUpdatedPost,
      // if successful, run this function
      success: response => {
        console.log(ourUpdatedPost);
        this.makeNoteReadOnly(thisNote);
        console.log("congrats");
        console.log(response);
      },
      error: response => {
        console.log("sorry");
        console.log(response);
      }
    });
  }

  editNote(e) {
    var thisNote = $(e.target).parents("li");
    // data-state is coming from methods below
    if (thisNote.data("state") == "editable") {
      //read only. Remember to pass thisNote to methods below
      this.makeNoteReadOnly(thisNote);
    } else {
      //make editable
      this.makeNoteEditable(thisNote);
    }
  }
  //two methods below toggle between making content editable or read only
  makeNoteEditable(thisNote) {
    thisNote
      .find(".edit-note")
      .html('<i class="fa fa-times" aria-hidden="true"></i>Cancel');
    thisNote
      .find(".note-title-field, .note-body-field")
      .removeAttr("readonly")
      .addClass("note-active-field");
    // adds the 'Save' button when note is being edited
    thisNote.find(".update-note").addClass("update-note--visible");
    // when 'edit' gets clicked on, add data-state='editable'
    thisNote.data("state", "editable");
  }

  makeNoteReadOnly(thisNote) {
    thisNote
      .find(".edit-note")
      .html('<i class="fa fa-pencil" aria-hidden="true"></i>Edit');
    thisNote
      .find(".note-title-field, .note-body-field")
      .attr("readonly", "readonly")
      .removeClass("note-active-field");
    // removes the 'Save' button when note is being edited
    thisNote.find(".update-note").removeClass("update-note--visible");
    thisNote.data("state", "cancel");
  }
}

// fifth export (then import in scripts.js)
export default MyNotes;
