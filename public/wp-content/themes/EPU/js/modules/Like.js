import $ from "jquery";

class Like {
  constructor() {
    this.events();
  }

  events() {
    $(".like-box").on("click", this.ourClickDispatcher.bind(this));
  }

  //methods
  // method checks whether to add or remove a 'like'
  ourClickDispatcher(e) {
    // whatever element got clicked on (icon, like count, gray box..) effect the 'like-box' that is closest
    const currentLikeBox = $(e.target).closest(".like-box");
    // if the like-box has a data attribute of data-exists='yes'
    if (currentLikeBox.attr("data-exists") == "yes") {
      this.deleteLike(currentLikeBox);
    } else {
      this.createLike(currentLikeBox);
    }
  }

  //create a like - function from above. pass currentLikeBox down
  createLike(currentLikeBox) {
    $.ajax({
      beforeSend: xhr => {
        // nonce = number used once. Provided by WP from 'wp_localize_scripts' in functions.php. Used to confirm user is who they say they are
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url: universityData.root_url + "/wp-json/university/v1/manageLike",
      type: "POST",
      // data gets passed to like-route.php
      data: { professorId: currentLikeBox.data("professor") },
      success: response => {
        currentLikeBox.attr("data-exists", "yes");
        let likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
        likeCount++;
        currentLikeBox.find(".like-count").html(likeCount);
        // add data attr. reponse is ID of 'like'
        currentLikeBox.attr("data-like", response);
        console.log(response);
      },
      error: response => {
        console.log(response);
      }
    });
  }
  //delete a like
  deleteLike(currentLikeBox) {
    $.ajax({
      beforeSend: xhr => {
        // nonce = number used once. Provided by WP from 'wp_localize_scripts' in functions.php. Used to confirm user is who they say they are
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url: universityData.root_url + "/wp-json/university/v1/manageLike",
      data: { like: currentLikeBox.attr("data-like") },
      type: "DELETE",
      success: response => {
        currentLikeBox.attr("data-exists", "no");
        let likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
        likeCount--;
        currentLikeBox.find(".like-count").html(likeCount);
        // add data attr. reponse is ID of 'like'
        currentLikeBox.attr("data-like", "");
        console.log(response);
      },
      error: response => {
        console.log(response);
      }
    });
  }
}

export default Like;
