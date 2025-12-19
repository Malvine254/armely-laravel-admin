document.addEventListener("DOMContentLoaded", function () {
    let lazyVideos = document.querySelectorAll(".lazy-video");

    lazyVideos.forEach(video => {
        video.addEventListener("click", function () {
            let iframe = document.createElement("iframe");
            iframe.setAttribute("width", "560");
            iframe.setAttribute("height", "315");
            iframe.setAttribute("src", video.getAttribute("data-src"));
            iframe.setAttribute("frameborder", "0");
            iframe.setAttribute("allow", "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share");
            iframe.setAttribute("allowfullscreen", "true");

            // Remove existing children (thumbnail + button)
            video.innerHTML = "";
            video.appendChild(iframe);
        });
    });
});


document.addEventListener("DOMContentLoaded", function () {
    if (!("IntersectionObserver" in window)) {
        console.warn("IntersectionObserver not supported. Consider a polyfill.");
        return;
    }

    // Lazy load normal images
    let lazyImages = document.querySelectorAll(".lazy-img");
    let lazyBGs = document.querySelectorAll(".lazy-bg");

    let observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                let target = entry.target;

                if (target.tagName === "IMG") {
                    let dataSrc = target.getAttribute("data-src");
                    
                    if (dataSrc) {
                        target.src = dataSrc;
                        target.removeAttribute("data-src");
                        target.classList.add("loaded");
                        
                        // Optional: Add an error handler for broken images
                        target.onerror = function () {
                            console.error("Image failed to load:", dataSrc);
                            target.classList.add("error"); // Apply error styling if needed
                        };
                    }
                } else {
                    let dataBg = target.getAttribute("data-bg");

                    if (dataBg) {
                        target.style.backgroundImage = "url('" + dataBg + "')";
                        target.removeAttribute("data-bg");
                        target.classList.add("loaded");
                    }
                }

                observer.unobserve(target);
            }
        });
    });

    lazyImages.forEach(img => observer.observe(img));
    lazyBGs.forEach(bg => observer.observe(bg));
});


$(document).ready(()=>{
	$('#searchInput').on('keyup', function() {
       
        var searchText = $(this).val().toLowerCase();
        $('#searchResults').empty();
        
        // Array of PHP page URLs
        var pageURLs = ['index', 'blog', 'services','team','applications','career','case-studies','events','customer-stories','company', 'contact','industries','privacy-policy', 'service-details','social-impact','social-impact-details','job-board'];
        var resultsCount = 0;
        // Loop through each page URL
        pageURLs.forEach(function(pageURL) {
            // Fetch the content of each PHP page using AJAX
            $.ajax({
                url: pageURL,
                success: function(response) {
                    var pageContent = $(response).text().toLowerCase();
                    var matches = pageContent.match(new RegExp(searchText, 'gi'));
                   
                    if (matches && resultsCount < 5) {
                        // If content found, display a link to the page with relevant snippets
                        var pageTitle = $(response).filter('title').text();
                        var snippets = getSnippets(pageContent, searchText);
                        $('#searchResults').append('<div>' + snippets + '<a class="default-color" href="' + pageURL + '"> Click here to access the content</a></div><hr>');
                         resultsCount++;
                    }
                }
            });
        });
         // Check if no results were found
        // if (resultsCount === 0) {
        //     $('#searchResults').append('<div>No results found</div>');
        // }
    });
// Function to get snippets of content surrounding the matched search term
function getSnippets(content, searchText) {
    var maxSnippetLength = 200; // Maximum length of each snippet
    var snippetCount = 3; // Number of snippets to display
    
    var snippets = [];
    var regex = new RegExp('(?:\\s|^)(.{0,' + maxSnippetLength + '}\\b' + searchText + '\\b.{0,' + maxSnippetLength + '})(?=\\s|$)', 'gi');
    var match;
    var snippetIndex = 0;
    
    while ((match = regex.exec(content)) !== null && snippetIndex < snippetCount) {
        snippets.push(match[1]);
        snippetIndex++;
    }
    
    return snippets.join(' ... ');
}
// search bar for blogs
$('#searchBar').on('input', function() {
    var filter = $(this).val().toLowerCase();
    var matchedItems = 0;
    $('#noResults').hide();

    $('.data-item').each(function() {
        var text = $(this).text().toLowerCase(); 
        if (text.includes(filter)) {
            $(this).show();
            matchedItems++;
        } else {
            $(this).hide();
        }
    });

    // Show or hide the "No results found" message
    if (matchedItems === 0) {
        $('#noResults').show();
    } else {
        $('#noResults').hide();
    }
});


})

//start of cookies 
// Automatically show the snackbar after a delay 
setTimeout(function() {
    $('#snackbar').addClass('show');
}, 1000);

// Close button functionality
$('.btn-close').click(function() {
    $('#snackbar').removeClass('show');
});



    // Function to check if the cookie ID is present and hide the snackbar
    function checkCookieAndHideSnackbar() {
        var userID = getCookie('userID');
        if (userID) {
         $('#snackbar').css("display", "none");
        }
    }

    // Call the function initially to hide the snackbar if the cookie ID is already present
    

    // Function to periodically check for the presence of the cookie ID and hide the snackbar
    setInterval(()=>{
        checkCookieAndHideSnackbar();
    }, 1000); // Check every 3 seconds (adjust as needed)
    
    // Rest of your code
    $('#acceptAll').on('click', function() {
        setCookie('userConsent', 'accepted', 365);
        trackUser();
        
    });

    $('#saveAllPreferences').on('click', function() {
        setCookie('userConsent', 'accepted', 365);
        trackUser();
    });

    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0,
                v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    function trackUser() {
        var userID = getCookie('userID');
        if (!userID) {
            userID = generateUUID();
            setCookie('userID', userID, 365);
        }

        $.get("php/cookies.php", { userID: userID }, function(data) {
            console.log("User tracked:", data);
        }).fail(function(xhr, status, error) {
            console.error("Error tracking user:", error);
        });
    }


// Submit contact form
$('#contact-form').submit(function(event) {
  event.preventDefault(); // Prevent default form submission

  // Clear previous messages
  $('#SubmitMessage').html('').removeClass('alert-success alert-danger alert').hide();

  // Get reCAPTCHA token
  var recaptchaResponse = grecaptcha.getResponse();
  if (!recaptchaResponse) {
    $('#SubmitMessage')
      .addClass('alert alert-danger alert-dismissible fade show')
      .html('<strong>Error:</strong> Please verify that you are not a robot.' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>')
      .show();
    return;
  }

  // Show loading message
  Swal.fire({
    title: 'Loading...',
    text: 'Please wait while we process your request.',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // Get serialized form data and append reCAPTCHA token
  var formData = $(this).serialize() + '&g-recaptcha-response=' + recaptchaResponse;

  // AJAX request
  $.ajax({
    type: 'POST',
    url: 'php/actions', // Replace with your actual endpoint
    data: formData,
    success: function(response3) {
      Swal.close(); // Close loading modal

      // Trim response to remove any whitespace/newlines
      var cleanResponse = String(response3).trim();
      console.log('Raw response:', JSON.stringify(response3));
      console.log('Cleaned response:', JSON.stringify(cleanResponse));
      console.log('Response length:', cleanResponse.length);
      console.log('Response charCodes:', Array.from(cleanResponse).map(c => c.charCodeAt(0)));
      console.log('Is "80"?', cleanResponse === "80");
      console.log('Includes "80"?', cleanResponse.includes("80"));

      $('#SubmitMessage').removeClass('alert-success alert-danger alert').hide();

      // Check if response contains "80" or is exactly "80"
      if (cleanResponse === "80" || cleanResponse.indexOf("80") !== -1) {
        console.log('✅ Form submission successful - firing gtag events');
        
        $('#SubmitMessage')
          .addClass('alert alert-success')
          .html('✅ Message was sent successfully!')
          .show();

        // Reset form and CAPTCHA
        $("#contact-form")[0].reset();
        grecaptcha.reset();

        // Google Analytics and Google Ads conversion tracking
        console.log('Checking gtag availability...');
        if (typeof gtag === 'function') {
          console.log('✅ gtag function is available');
          
          // Fire page_view event
          gtag('event', 'page_view', {
            page_title: 'Thank You',
            page_location: window.location.origin + '/thank-you',
            page_path: '/thank-you'
          });
          console.log('📊 page_view event fired');

          // Fire custom event
          gtag('event', 'contact_form_submitted', {
            event_category: 'Contact',
            event_label: 'Contact Form AJAX',
            value: 1
          });
          console.log('📝 contact_form_submitted event fired');

          // Fire conversion event
          gtag('event', 'conversion', {
            send_to: 'AW-16698949072/p78BCKGBjaobEND71po-'
          });
          console.log('🎯 conversion event fired');
        } else {
          console.error('❌ gtag function not available');
        }

      } else {
        console.warn('⚠️ Form submission failed or returned unexpected response:', cleanResponse);
        $('#SubmitMessage')
          .addClass('alert alert-danger alert-dismissible fade show')
          .html('<strong>Warning:</strong> ' + response3 +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>')
          .show();
      }

      console.log('Form submission complete');
    },
    error: function(error) {
      Swal.close();
      $('#SubmitMessage')
        .addClass('alert alert-danger alert-dismissible fade show')
        .html('<strong>Error:</strong> Form submission failed. Please try again.' +
              '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>')
        .show();
      console.error('Form submission error:', error);
    }
  });
});


//start of read more for cards
 $(document).ready(function(){
    $('.shorten-content').each(function(){
        const contentElement = $(this);
        const fullContent = contentElement.html().trim();
        const maxLength = 100;
        let shortContent = fullContent;

        if (fullContent.length > maxLength) {
            shortContent = fullContent.substring(0, maxLength) + '...';
            contentElement.html(shortContent);
        }

        contentElement.next('.read-more-btn').click(function(e){
            e.preventDefault();
            
            if(contentElement.text().includes('...')) {
                contentElement.html(fullContent);
                $(this).html('<strong>READ LESS <i class="fa fa-long-arrow-right"></i></strong>');
            } else {
                contentElement.html(shortContent);
                $(this).html('<strong>READ MORE <i class="fa fa-long-arrow-right"></i></strong>');
            }
        });
    });
});
 // end of read more

 //start of google analytics code
 window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
//for google analytics
  gtag('config', 'G-4301EZWQ4C');

  //for google ads
  gtag('config', 'AW-16698949072');

//end of google analytics code


 gtag('config', 'AW-16698949072/p78BCKGBjaobEND71po-', {
    'phone_conversion_number': '+1 972 460 0643'
  });
  
 // start of partner slider
  $(".clients-carousel").owlCarousel({
    autoplay: true,
    dots: true,
    loop: true,
    responsive: {
      0: {
        items: 2
      },
      768: {
        items: 4
      },
      900: {
        items: 6
      }
    }
  });

$('#job-form').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Create FormData object
    var formData = new FormData();

    // Append file to FormData object
    var file = $('#cv')[0].files[0]; // Assuming 'cv' is the id of your file input
   

    // Append other form fields to FormData object
    formData.append('name', $('#name').val());
    formData.append('email', $('#email').val());
    formData.append('cv', file);
    formData.append('city', $('#city').val());
    formData.append('zip', $('#zip').val());
    formData.append('phone', $('#phone').val());
    formData.append('state', $('#state').val());
    formData.append('address', $('#address').val());
    formData.append('position', $('#position').val());
    formData.append('type', $('#type').val());
    // Display loading message before AJAX request
    Swal.fire({
      title: 'Loading...',
      text: 'Please wait while we process your request.',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    // Perform an AJAX request to submit the form data
    $.ajax({
      type: 'POST',
      url: 'php/actions', // Replace with your actual server-side endpoint
      data: formData, 
      processData: false, // Prevent jQuery from automatically processing data
      contentType: false,
      success: function(response) {
        // Handle the success response
        if (response==="1") {
          Swal.close(); // Close the loading message before showing the next one
            Swal.fire({
            title: 'Success!',
            text: "Applicatioin was Successfully",
            confirmButtonColor: 'rgb(47,85,151)', 
            icon: 'success',
          });
            $("#job-form")[0].reset();
        }else{
            Swal.fire({
              title: 'Warning',
              text: response,
              icon: 'warning',
              confirmButtonText: 'OK',
               confirmButtonColor: 'rgb(47,85,151)'
            });
        }


         
        console.log(response); // You can do something with the response data
      },
      error: function(error) {
        // Handle the error response
        console.error('Form submission error');
        console.error(error); // You can display an error message or perform other actions
      }
    });
  }); 


// submit offers form
$('#offers-form').submit(function(event) {
event.preventDefault(); // Prevent the default form submission
// Retrieve the form data
var formData2 = $(this).serialize();

// Display loading message before AJAX request
Swal.fire({
  title: 'Loading...',
  text: 'Please wait while we process your request.',
  allowOutsideClick: false,
  didOpen: () => {
    Swal.showLoading();
  }
});

// Perform an AJAX request to submit the form data
$.ajax({
  type: 'POST',
  url: 'php/offers', // Replace with your actual server-side endpoint
  data: formData2,
  success: function(e) {
    Swal.close(); // Close the loading message before showing the next one
    
    // Handle the success response
    if (e === "1") {
      $("#offers-form")[0].reset();
      Swal.fire({
        title: 'Thank you!',
        text: "Please check your email inbox or spam folder for the download link.",
        confirmButtonColor: 'rgb(47,85,151)', 
        icon: 'success',
      });
    } else {
      Swal.fire({
        title: 'Warning',
        text: e,
        icon: 'warning',
        confirmButtonText: 'OK',
        confirmButtonColor: 'rgb(47,85,151)'
      });
    }

    console.log(e); // You can do something with the response data
  },
  
  error: function(error) {
    Swal.close(); // Close the loading message in case of an error
    // Handle the error response
    Swal.fire({
      title: 'Error',
      text: 'Form submission failed. Please try again.',
      icon: 'error',
      confirmButtonText: 'OK',
      confirmButtonColor: 'rgb(47,85,151)'
    });
    console.error('Form submission error');
    console.error(error); // You can display an error message or perform other actions
  }
});



});

// submit sql offers form
$('#sql-offer-form').submit(function(event) {
event.preventDefault(); // Prevent the default form submission
// Retrieve the form data
var formData2 = $(this).serialize();

// Display loading message before AJAX request
Swal.fire({
  title: 'Loading...',
  text: 'Please wait while we process your request.',
  allowOutsideClick: false,
  didOpen: () => {
    Swal.showLoading();
  }
});

// Perform an AJAX request to submit the form data
$.ajax({
  type: 'POST',
  url: 'php/offers', // Replace with your actual server-side endpoint
  data: formData2,
  success: function(e) {
    Swal.close(); // Close the loading message before showing the next one
    
    // Handle the success response
    if (e === "1") {
      $("#sql-offer-form")[0].reset(); // Reset the form after successful submission
      Swal.fire({
        title: 'Thank you!',
        text: "Request sent successfully.",
        confirmButtonColor: 'rgb(47,85,151)', 
        icon: 'success',
      });
    } else {
      Swal.fire({
        title: 'Warning',
        text: e,
        icon: 'warning',
        confirmButtonText: 'OK',
        confirmButtonColor: 'rgb(47,85,151)'
      });
    }

    console.log(e); // You can do something with the response data
  },
  
  error: function(error) {
    Swal.close(); // Close the loading message in case of an error
    // Handle the error response
    Swal.fire({
      title: 'Error',
      text: 'Form submission failed. Please try again.',
      icon: 'error',
      confirmButtonText: 'OK',
      confirmButtonColor: 'rgb(47,85,151)'
    });
    console.error('Form submission error');
    console.error(error); // You can display an error message or perform other actions
  }
});



});

// submit sql offers form
$('#coe-offer-form').submit(function(event) {
event.preventDefault(); // Prevent the default form submission
// Retrieve the form data
var formData2 = $(this).serialize();

// Display loading message before AJAX request
Swal.fire({
  title: 'Loading...',
  text: 'Please wait while we process your request.',
  allowOutsideClick: false,
  didOpen: () => {
    Swal.showLoading();
  }
});

// Perform an AJAX request to submit the form data
$.ajax({
  type: 'POST',
  url: 'php/offers', // Replace with your actual server-side endpoint
  data: formData2,
  success: function(e) {
    Swal.close(); // Close the loading message before showing the next one
    
    // Handle the success response
    if (e === "1") {
      $("#coe-offer-form")[0].reset(); // Reset the form after successful submission
      Swal.fire({
        title: 'Thank you!',
        text: "Request sent successfully.",
        confirmButtonColor: 'rgb(47,85,151)', 
        icon: 'success',
      });
    } else {
      Swal.fire({
        title: 'Warning',
        text: e,
        icon: 'warning',
        confirmButtonText: 'OK',
        confirmButtonColor: 'rgb(47,85,151)'
      });
    }

    console.log(e); // You can do something with the response data
  },
  
  error: function(error) {
    Swal.close(); // Close the loading message in case of an error
    // Handle the error response
    Swal.fire({
      title: 'Error',
      text: 'Form submission failed. Please try again.',
      icon: 'error',
      confirmButtonText: 'OK',
      confirmButtonColor: 'rgb(47,85,151)'
    });
    console.error('Form submission error');
    console.error(error); // You can display an error message or perform other actions
  }
});

});
//close the anouncement banner
 function closeBanner() {
        document.getElementById('announcementBanner').style.display = 'none';
}


// scroll blog

$(document).ready(function () {
  // === SHARE BUTTON LOGIC ===
  $(".shareBtn").click(function () {
    var social = $(this).data("social");
    var url = encodeURIComponent(window.location.href);
    var title = $("#blogTitle").text();
    var shareURL;

    switch (social) {
      case "facebook":
        shareURL = "https://www.facebook.com/sharer/sharer.php?u=" + url;
        break;
      case "twitter":
        shareURL = "https://twitter.com/intent/tweet?url=" + url + "&text=" + title;
        break;
      case "linkedin":
        shareURL = "https://www.linkedin.com/shareArticle?mini=true&url=" + url + "&title=" + title;
        break;
      case "instagram":
        alert("Instagram sharing via browser not supported.");
        return;
    }

    window.open(shareURL, "_blank", "noopener,noreferrer,width=600,height=400");
  });

  // === SHOW MORE SCROLL LOGIC ===
  var contentDiv = $('.content');
  var showMoreButton = $('.show-more');

  function checkScrollable() {
    if (contentDiv[0] && contentDiv[0].scrollHeight > contentDiv.innerHeight()) {
      showMoreButton.show();
    } else {
      showMoreButton.hide();
    }
  }

  checkScrollable();

  showMoreButton.on('click', function () {
    contentDiv.animate({
      scrollTop: contentDiv[0].scrollHeight
    }, 800, function () {
      checkScrollable();
    });
  });

  contentDiv.on('scroll', function () {
    if (contentDiv.scrollTop() + contentDiv.innerHeight() >= contentDiv[0].scrollHeight - 1) {
      showMoreButton.fadeOut();
    } else {
      showMoreButton.fadeIn();
    }
  });

  // === TEXT-TO-SPEECH LOGIC ===
  const speechSynthesisInstance = window.speechSynthesis;
  let voices = [];
  let isSpeaking = false;
  let isPaused = false;
  let lines = [];
  let currentIndex = 0;
  let currentUtterance;

  // Load voices properly (wait for them)
  function loadVoices() {
    voices = speechSynthesisInstance.getVoices();
    if (!voices.length) {
      speechSynthesisInstance.onvoiceschanged = () => {
        voices = speechSynthesisInstance.getVoices();
      };
    }
  }

  loadVoices();

  $('#toggleSpeech').click(function () {
    if (!isSpeaking) {
      speak();
    } else if (!isPaused) {
      pause();
    } else {
      resume();
    }
  });

  function speak() {
    const text = $('#blog-content').text();
    lines = text.split(/\n+|\.\s+/).filter(line => line.trim() !== '');
    currentIndex = 0;
    isSpeaking = true;
    isPaused = false;
    speakLine();
  }

  function speakLine() {
    if (currentIndex < lines.length) {
      const line = lines[currentIndex].trim();
      const utterance = new SpeechSynthesisUtterance(line);
      utterance.voice = voices[0] || null;

      utterance.onstart = () => highlightCurrentLine(line);
      utterance.onend = () => {
        removeHighlight();
        currentIndex++;
        if (currentIndex < lines.length && !isPaused) {
          speakLine();
        } else {
          isSpeaking = false;
          isPaused = false;
        }
      };

      currentUtterance = utterance;
      $('#volume-icons').removeClass('fa-volume-high').addClass('fa-volume-xmark');
      speechSynthesisInstance.speak(utterance);
    }
  }

  function pause() {
    if (speechSynthesisInstance.speaking) {
      speechSynthesisInstance.pause();
      isPaused = true;
      $('#volume-icons').removeClass('fa-volume-xmark').addClass('fa-volume-high');
    }
  }

  function resume() {
    if (isPaused) {
      speechSynthesisInstance.resume();
      isPaused = false;
      $('#volume-icons').removeClass('fa-volume-high').addClass('fa-volume-xmark');
    }
  }

  function highlightCurrentLine(line) {
    removeHighlight();
    const escapedLine = line.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const content = $('#blog-content').html();
    const highlighted = content.replace(new RegExp(escapedLine), `<span class="bg-warning">${line}</span>`);
    $('#blog-content').html(highlighted);
  }

  function removeHighlight() {
    $('#blog-content .bg-warning').contents().unwrap();
  }
});


// submit campaign form 
$('#campaign-form').submit(function(event) {
  event.preventDefault(); // Prevent default submission

  // ✅ Get reCAPTCHA token
  var recaptchaResponse = grecaptcha.getResponse();
  if (!recaptchaResponse) {
    Swal.fire({
      title: 'CAPTCHA Required',
      text: 'Please verify that you are not a robot.',
      icon: 'warning',
      confirmButtonColor: 'rgb(47,85,151)'
    });
    return;
  }

  // ✅ Show loading message
  Swal.fire({
    title: 'Loading...',
    text: 'Please wait while we process your request.',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });


  
  // ✅ Get serialized form data and append reCAPTCHA token
  var formData = $(this).serialize() + '&g-recaptcha-response=' + recaptchaResponse;

  // ✅ AJAX request
  $.ajax({
    type: 'POST',
    url: 'php/actions', // same endpoint
    data: formData,
    success: function(response) {
      Swal.close(); 
      if (response === "80") {
        Swal.fire({
          title: 'Success!',
          text: "Campaign form submitted successfully",
          confirmButtonColor: 'rgb(47,85,151)',
          icon: 'success',
        });
        $("#campaign-form")[0].reset();
        grecaptcha.reset(); 
      } else {
        Swal.fire({
          title: 'Warning',
          text: response,
          icon: 'warning',
          confirmButtonText: 'OK',
          confirmButtonColor: 'rgb(47,85,151)'
        });
      }
      console.log(response);
    },
    error: function(error) {
      Swal.close();
      Swal.fire({
        title: 'Error!',
        text: 'Form submission failed. Please try again.',
        icon: 'error',
        confirmButtonColor: 'rgb(47,85,151)'
      });
      console.error('Form submission error:', error);
    }
  });
});


// end of campaign form

// Chat bot modal section code


    const $popup = $("#helpPopup");
    const $bubble = $("#chatBubble");
    const $modal = $("#myModal");

    const $chatNowBtn = $("#chatNowBtn");
    const $noThanksBtn = $("#noThanksBtn");
    const $closeModal = $(".close");

    // Open chat modal
    function openChat() {
        $modal.show();
    }

    // Main CTA: Chat Now
    $chatNowBtn.on("click", function () {
        $popup.hide();
        openChat();
    });

    // No thanks → hide popup, show bubble
    $noThanksBtn.on("click", function () {
        $popup.hide();
        $bubble.css("display", "flex");
    });

    // Clicking the small bubble → open chat
    $bubble.on("click", function () {
        openChat();
    });

    // Close modal
    $closeModal.on("click", function () {
        $modal.hide();
        $bubble.css("display", "flex");
    });

    // Clicking outside the modal closes it
    $(window).on("click", function (event) {
        if ($(event.target).is($modal)) {
            $modal.hide();
            $bubble.css("display", "flex");
        }
    });

