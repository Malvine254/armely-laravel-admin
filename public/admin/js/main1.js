$('#addBlogTable').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission
    // Display loading message before AJAX request
    Swal.fire({
      title: 'Loading...',
      text: 'Please wait while we process your request.',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    var formData = new FormData();
    // Retrieve the form data
    var editorData = CKEDITOR.instances.blogBody.getData();
    formData.append("body", editorData);

    // Perform an AJAX request to submit the form data
    $.ajax({
      type: 'POST',
      url: 'php/uploads.php', 
      data: formData,
      contentType: false, // Required for FormData
      processData: false, // Required for FormData
      success: function(response34) {
        // Handle the success response
        if (response34) {
          Swal.close();
         
          Swal.fire({
            title: 'Success!',
            text: response34,
            confirmButtonColor: 'rgb(47,85,151)', 
            icon: 'success',
          });
          $("#addBlogTable")[0].reset(); // Reset the form
          CKEDITOR.instances.blogBody.setData(''); // Reset the CKEditor
        }

        // else {
        //   Swal.fire({
        //     title: 'Warning',
        //     text: response34,
        //     icon: 'warning',
        //     confirmButtonText: 'OK',
        //     confirmButtonColor: 'rgb(47,85,151)'
        //   });
        // }
         // You can do something with the response data
      }

      ,
      error: function(error) {
        // Handle the error response
        console.error('Form submission error');
        console.error(error); // You can display an error message or perform other actions
        Swal.fire({
          title: 'Error',
          text: 'An error occurred while submitting the form.',
          icon: 'error',
          confirmButtonText: 'OK',
          confirmButtonColor: 'rgb(47,85,151)'
        });
      }
    });
});


// .........................start of submit new blog articles...........................

$('#addBlogForm').submit(function(event) {
  event.preventDefault(); // Prevent the default form submission

  Swal.fire({
    title: 'Loading...',
    text: 'Please wait while we process your request.',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // Create FormData object
  var formData = new FormData();

  // Append CKEditor content and image
  var blog_image = $('#blog_image')[0].files[0];
  var blogBody = CKEDITOR.instances['blogBody'].getData();

  formData.append('blog_title', $('#blog_title').val());
  formData.append('blog_body', blogBody);
  formData.append('blog_image', blog_image);

  // Get `editId` and `type` from URL
  const urlParams = new URLSearchParams(window.location.search);
  const editId = urlParams.get('editId');
  const type = urlParams.get('type');

  // Build request URL based on GET parameters
  let requestUrl = 'php/uploads.php';
  if (editId && type === 'blog') {
    requestUrl += `?editId=${encodeURIComponent(editId)}&type=blog`;
  }

  // Perform AJAX request
  $.ajax({
    type: 'POST',
    url: requestUrl,
    data: formData,
    processData: false,
    contentType: false,
    success: function(response) {
      response = $.trim(response);
      Swal.close();

      if (response === "1" || response.toLowerCase().includes("updated")) {
        Swal.fire({
          title: 'Success!',
          text: "Blog was updated successfully.",
          icon: 'success',
          confirmButtonColor: 'rgb(47,85,151)'
        });
      } else if (response.toLowerCase().includes("inserted")) {
        Swal.fire({
          title: 'Success!',
          text: "Blog was inserted successfully.",
          icon: 'success',
          confirmButtonColor: 'rgb(47,85,151)'
        });
      } else {
        Swal.fire({
          title: 'Warning',
          text: response,
          icon: 'warning',
          confirmButtonText: 'OK',
          confirmButtonColor: 'rgb(47,85,151)'
        });
      }

      // Reset form after any valid action
      $("#addBlogForm")[0].reset();
      CKEDITOR.instances.blogBody.setData('');
    },
    error: function(error) {
      console.error('Form submission error:', error);
      Swal.fire({
        title: 'Error',
        text: 'An error occurred while submitting the form.',
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: 'rgb(47,85,151)'
      });
    }
  });
});

// .........................end of submit new blog articles...........................

// .........................start of submit new blog articles...........................

$('#customerStoriesForm').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Create FormData object
    var formData = new FormData();

    // Append file to FormData object
    var profile = $('#profile')[0].files[0]; // Assuming 'cv' is the id of your file input
    var body_content = CKEDITOR.instances['editCustomerBody'].getData();
   
    // Append other form fields to FormData object
    formData.append('position', $('#clientPosition').val());
    formData.append('name', $('#clientName').val())
    formData.append('profile', profile);
    formData.append('body_content', body_content);
    //alert(blogBody)
    // Perform an AJAX request to submit the form data
    $.ajax({
      type: 'POST',
      url: 'php/uploads', // Replace with your actual server-side endpoint
      data: formData,
      processData: false, // Prevent jQuery from automatically processing data
      contentType: false,
      success: function(response) {
        // Handle the success response
        if (response==="1") {
          Swal.fire({
          title: 'Success!',
          text: "Customer Stories was updated successfully",
          confirmButtonColor: 'rgb(47,85,151)', 
          icon: 'success',
        });
          $("#customerStoriesForm")[0].reset();
          CKEDITOR.instances.editCustomerBody.setData(''); 
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

// .........................end of submit new blog articles...........................

// .........................start of submit new career banner...........................

$('#careerForm').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Create FormData object
    var formData = new FormData();

    // Append file to FormData object
    var date = $('#career_image') // Assuming 'cv' is the id of your file input
    var career_body = CKEDITOR.instances['careerBody'].getData();
   
    // Append other form fields to FormData object
    formData.append('career_title', $('#career_title').val());
    formData.append('name', $('#clientName').val())
    formData.append('career_body', career_body);
    //alert(blogBody)
    // Perform an AJAX request to submit the form data
    $.ajax({
      type: 'POST',
      url: 'php/uploads', // Replace with your actual server-side endpoint
      data: formData,
      processData: false, // Prevent jQuery from automatically processing data
      contentType: false,
      success: function(response) {
        // Handle the success response
        if (response==="1") {
          Swal.fire({
          title: 'Success!',
          text: "Career Banner was updated successfully",
          confirmButtonColor: 'rgb(47,85,151)', 
          icon: 'success',
        });
          $("#careerForm")[0].reset();
          CKEDITOR.instances.careerBody.setData(''); 
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

// .........................end of submit new career banner..........................

// .........................start of submit new blog articles...........................

$('#footerForm').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Create FormData object
    var formData = new FormData();

    // Append file to FormData object
   
    var footerBody = CKEDITOR.instances['footerBody'].getData();
   
    // Append other form fields to FormData object
  
    formData.append('footerBody', footerBody);
    //alert(blogBody)
    // Perform an AJAX request to submit the form data
    $.ajax({
      type: 'POST',
      url: 'php/uploads', // Replace with your actual server-side endpoint
      data: formData,
      processData: false, // Prevent jQuery from automatically processing data
      contentType: false,
      success: function(response) {
        // Handle the success response
        if (response==="1") {
          Swal.fire({
          title: 'Success!',
          text: "Footer was updated successfully",
          confirmButtonColor: 'rgb(47,85,151)', 
          icon: 'success',
        });
          $("#footerForm")[0].reset();
          CKEDITOR.instances.footerBody.setData(''); 
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

// .........................end of submit new blog articles...........................

$('#youtubeVideoForm').submit(function(event) {
  event.preventDefault(); // Prevent the default form submission

  // Create FormData object
  var formData = new FormData();

  // Get iframe value
  var iframeContents = $("#iframeContents").val();
  formData.append('iframeContents', iframeContents);

  // Get GET parameters from URL
  const urlParams = new URLSearchParams(window.location.search);
  const editId = urlParams.get('editId');
  const type = urlParams.get('type');

  // Build request URL
  let requestUrl = 'php/uploads.php';
  if (editId && type === 'video') {
    requestUrl += `?editId=${encodeURIComponent(editId)}&type=video`;
  }

  // Send AJAX request
  $.ajax({
    type: 'POST',
    url: requestUrl,
    data: formData,
    processData: false,
    contentType: false,
    success: function(response2) {
      if (response2 === "1" || response2.toLowerCase().includes("updated") || response2.toLowerCase().includes("inserted")) {
        Swal.fire({
          title: 'Success!',
          text: "Operation was completed successfully.",
          icon: 'success',
          confirmButtonColor: 'rgb(47,85,151)'
        });
        $("#youtubeVideoForm")[0].reset();
      } else {
        Swal.fire({
          title: 'Warning',
          text: response2,
          icon: 'warning',
          confirmButtonText: 'OK',
          confirmButtonColor: 'rgb(47,85,151)'
        });
      }

      console.log(response2);
    },
    error: function(error) {
      console.error('Form submission error:', error);
      Swal.fire({
        title: 'Error',
        text: 'An error occurred while submitting the form.',
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: 'rgb(47,85,151)'
      });
    }
  });
});


// submit contact form
$('#reset_pass_form').submit(function(event) {
event.preventDefault(); // Prevent the default form submission
// Display loading message before AJAX request
Swal.fire({
  title: 'Loading...',
  text: 'Please wait while we process your request.',
  allowOutsideClick: false,
  didOpen: () => {
    Swal.showLoading();
  }
});
// Retrieve the form data
var formData = $(this).serialize();

// Perform an AJAX request to submit the form data
$.ajax({
  type: 'POST',
  url: 'php/uploads', // Replace with your actual server-side endpoint
  data: formData,
  success: function(response3) {
    // Handle the success response
    if (response3==="100") {
      Swal.close(); // Close the loading message before showing the next one
      Swal.fire({
      title: 'Success!',
      text: "One time password was sent successfully, check your inbox or spam folder.",
      confirmButtonColor: 'rgb(47,85,151)', 
      icon: 'success',
    });
      $("#contact-form")[0].reset();
    }else{
      Swal.fire({
    title: 'Warning',
    text: response3,
    icon: 'warning',
    confirmButtonText: 'OK',
     confirmButtonColor: 'rgb(47,85,151)'
  });
    }


     
    console.log(response3); // You can do something with the response data
  },
  error: function(error) {
    // Handle the error response
    console.error('Form submission error');
    console.error(error); // You can display an error message or perform other actions
  }
});
});

$('#eventsForm').submit(function(event) { 
    event.preventDefault(); // Prevent the default form submission
    // Display loading message before AJAX request
      Swal.fire({
        title: 'Loading...',
        text: 'Please wait while we process your request.',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
    // Retrieve the form data
    var formData = $(this).serialize();

    // Perform an AJAX request to submit the form data
    $.ajax({
      type: 'POST',
      url: 'php/uploads', // Replace with your actual server-side endpoint
      data: formData,
      success: function(response2) {
      Swal.close(); // Close the loading message before showing the next one
      // Trim the response to remove any leading or trailing spaces
      response2 = $.trim(response2); 

        // Handle the success response
        if (response2==="3") { 
          Swal.fire({
          title: 'Success!',
          text: "Event was added successfully",
          confirmButtonColor: 'rgb(47,85,151)', 
          icon: 'success',
        });
          $("#eventsForm")[0].reset();
        }else{
          Swal.fire({
          title: 'Warning',
          text: response2,
          icon: 'warning',
          confirmButtonText: 'OK',
           confirmButtonColor: 'rgb(47,85,151)'
        });
        }


         
        console.log(response2); // You can do something with the response data
      },
      error: function(error) {
        // Handle the error response
        console.error('Form submission error');
        console.error(error); // You can display an error message or perform other actions
      }
    });
  });

// submit teams form
$('#teamForm').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Create FormData object
    var formData = new FormData();

    // Append file to FormData object
    var file = $('#team_image')[0].files[0]; // Assuming 'cv' is the id of your file input
   

    // Append other form fields to FormData object
    formData.append('team_name', $('#team_name').val());
    formData.append('team_title', $('#team_title').val());
    formData.append('team_image', file);
    formData.append('team_body', $('#team_body').val());
    formData.append('x', $('#x').val());
    formData.append('instagram', $('#instagram').val());
    formData.append('facebook', $('#facebook').val());
    formData.append('linkedin', $('#linkedin').val());
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
      url: 'php/uploads', // Replace with your actual server-side endpoint
      data: formData, 
      processData: false, // Prevent jQuery from automatically processing data
      contentType: false,
      success: function(response9) {
        // Handle the success response
        Swal.close(); // Close the loading message before showing the next one
            Swal.fire({
            title: 'Success!',
            text: "Applicatioin was Successfully",
            confirmButtonColor: 'rgb(47,85,151)', 
            icon: 'success',
          });
            $("#teamForm")[0].reset();


         
        
      },
      error: function(error) {
        // Handle the error response
        console.error('Form submission error');
        console.error(error); // You can display an error message or perform other actions
      }
    });
  }); 

// Add new social impact

 
$('#socialImpactForm').submit(function(event) {
  event.preventDefault();

  var form = $('#socialImpactForm')[0];
  var formData = new FormData(form);

  // Manually get CKEditor content (overwrite textarea value)
  formData.set('body_content', CKEDITOR.instances['newSocialImpactBody'].getData());

  $.ajax({
    type: 'POST',
    url: 'php/uploads.php', // Match this with your PHP script path
    data: formData,
    processData: false,
    contentType: false,
    success: function(response) {
      try {
        const res = JSON.parse(response); // If you return JSON
        if (res.success) {
          Swal.fire({
            title: 'Success!',
            text: res.message,
            icon: 'success',
            confirmButtonColor: 'rgb(47,85,151)'
          });
          $('#socialImpactForm')[0].reset();
          CKEDITOR.instances.newSocialImpactBody.setData('');
        } else {
          Swal.fire({
            title: 'Error',
            text: res.message,
            icon: 'error',
            confirmButtonColor: 'rgb(47,85,151)'
          });
        }
      } catch (e) {
        console.error('Response is not valid JSON:', response);
        Swal.fire({
          title: 'Unexpected Response',
          text: response,
          icon: 'warning',
          confirmButtonColor: 'rgb(47,85,151)'
        });
      }
    },
    error: function(xhr) {
      Swal.fire({
        title: 'Submission Failed',
        text: xhr.statusText,
        icon: 'error',
        confirmButtonColor: 'rgb(47,85,151)'
      });
    }
  });
});

// .........................end of submit new career banner..........................




