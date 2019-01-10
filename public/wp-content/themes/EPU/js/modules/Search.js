import $ from "jquery";

class Search {
  // 1. Describe and create/initiate our object
  constructor() {
    this.addSearchHTML();
    this.resultsDiv = $("#search-overlay__results");
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.searchField = $("#search-term");
    this.isOverlayOpen = false;
    this.isSpinnerVisible = false;
    this.previousValue;
    this.typingTimer;
    // calling this.events() will add event listeners to page
    this.events();
  }
  // 2. Events -> connect the dots between what you're constructing and what functions you want to perform
  events() {
    this.openButton.on("click", this.openOverlay.bind(this));
    this.closeButton.on("click", this.closeOverlay.bind(this));
    $(document).on("keyup", this.keyPressDispatcher.bind(this));
    this.searchField.on("keyup", this.typingLogic.bind(this));
  }
  //  3. methods (function, action...)
  typingLogic() {
    //if the string value has been changed run the code. otherwise just chill
    if (this.previousValue != this.searchField.val()) {
      // clear the timer so function wont fire until the full wait time has been reached (keep from overloading search requests)
      clearTimeout(this.typingTimer);

      //will evaluate to 'true' if searchField has a value. if there is no value (field is blank) don't send off search request to WP servers
      if (this.searchField.val()) {
        //if the spinner isn't already spinning, let the spinning commence
        if (!this.isSpinnerVisible) {
          this.resultsDiv.html('<div class="spinner-loader"></div>');
          this.isSpinnerVisible = true;
        }
        // pause the results from showing to give spinner effect
        this.typingTimer = setTimeout(this.getResults.bind(this), 750);

        // if no value in searchField clear html/stop spinner
      } else {
        this.resultsDiv.html("");
        this.isSpinnerVisible = false;
      }
    }

    // update the value of the search field
    this.previousValue = this.searchField.val();
  }

  getResults() {
    $.getJSON(
      universityData.root_url +
        "/wp-json/university/v1/search?term=" +
        this.searchField.val(),
      results => {
        this.resultsDiv.html(`
        <div class="row">
          <div class="one-third">
            <h2 class="search-overlay__section-title">General Information</h2>
            ${
              results.generalInfo.length
                ? '<ul class="link-list min-list">'
                : "<p>No general information matches your search</p>"
            }
            ${results.generalInfo
              .map(
                item =>
                  `<li><a href="${item.permalink}">${item.title}</a> 
                  ${
                    item.postType == "post" ? `by ${item.authorName}` : ""
                  }</li>`
              )
              .join("")}
            ${results.generalInfo.length ? "</ul>" : ""}
          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-title">Programs</h2>
            ${
              results.programs.length
                ? '<ul class="link-list min-list">'
                : `<p>No programs match your search. <a href="${
                    universityData.root_url
                  }/programs">View All Programs</a></p>`
            }
            ${results.programs
              .map(
                item => `<li><a href="${item.permalink}">${item.title}</a></li>`
              )
              .join("")}
            ${results.programs.length ? "</ul>" : ""}
            <h2 class="search-overlay__section-title">Professors</h2>
            ${
              results.professors.length
                ? '<ul class="professor-cards">'
                : `<p>No programs match your search.</p>`
            }
            ${results.professors
              .map(
                item => `
                <li class="professor-card__list-item">
                  <a class="professor-card" href="${item.permalink}">
                    <img class="professor-card__image"src="${item.image}">
                    <span class="professor-card__name">${item.title}</span>
                  </a>
                </li>
              `
              )
              .join("")}
            ${results.professors.length ? "</ul>" : ""}
          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-title">Campuses</h2>
            ${
              results.campuses.length
                ? '<ul class="link-list min-list">'
                : `<p>No campuses match your search. <a href="${
                    universityData.root_url
                  }/campuses">View All Campuses</a></p>`
            }
            ${results.campuses
              .map(
                item => `<li><a href="${item.permalink}">${item.title}</a></li>`
              )
              .join("")}
            ${results.campuses.length ? "</ul>" : ""}
            <h2 class="search-overlay__section-title">Events</h2>
            ${
              results.events.length
                ? ""
                : `<p>No events match your search. <a href="${
                    universityData.root_url
                  }/events">View All Events</a></p>`
            }
            ${results.events
              .map(
                item => `
                <div class="event-summary">
                  <a class="event-summary__date t-center" href="${
                    item.permalink
                  }">
                    <span class="event-summary__month">${item.month}</span>
                    <span class="event-summary__day">${item.day}</span>  
                  </a>
                <div class="event-summary__content">
                  <h5 class="event-summary__title headline headline--tiny">
                    <a href="${item.permalink}">${item.title}</a>
                  </h5>
                    <p>${item.description}
                      <a href="${item.permalink}" class="nu gray">Learn more</a>
                    </p>
                  </div>
                </div>
                `
              )
              .join("")}
          </div>
        </div>
      `);
        this.isSpinnerVisible = false;
      }
    );
  }

  openOverlay() {
    this.searchOverlay.addClass("search-overlay--active");
    $("body").addClass("body-no-scroll");
    // resets searchfield before opening
    this.searchField.val("");
    // gives css time to load so browser can focus the input
    setTimeout(() => this.searchField.focus(), 301);
    this.isOverlayOpen = true;
    // by returning false we are stopping default behavior of using fallback search page
    return false;
  }

  closeOverlay() {
    this.searchOverlay.removeClass("search-overlay--active");
    $("body").removeClass("body-no-scroll");
    this.isOverlayOpen = false;
  }

  keyPressDispatcher(e) {
    // if user presses 's' AND the overlay isn't already open AND another input isn't being used- open search overlay
    if (
      e.keyCode == 83 &&
      !this.isOverlayOpen &&
      !$("input, textarea").is(":focus")
    ) {
      this.openOverlay();
    }
    //if user presses 'esc' and the overlay is open - close search overlay
    if (e.keyCode == 27 && this.isOverlayOpen) {
      this.closeOverlay();
    }
  }

  addSearchHTML() {
    $("body").append(`
      <div class="search-overlay">
        <div class="search-overlay__top">
          <div class="container">
            <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
            <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term">
            <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
          </div>
        </div>

      <div class="container">
        <div id="search-overlay__results"></div>
      </div>
    </div>
    `);
  }
}

export default Search;
