jQuery(document).ready(function ($) {
  $(".hbuz-poll-share").each(function () {
    var pollItem = $(this).closest(".hbuz-poll-item");
    var pollUrl = pollItem.data("permalink"); // Get the permalink directly from the data attribute

    // Set Facebook share link
    $(this)
      .find(".hbuz-poll-share-facebook")
      .attr(
        "href",
        "https://www.facebook.com/sharer/sharer.php?u=" +
          encodeURIComponent(pollUrl)
      );

    // Set Messenger share link
    $(this)
      .find(".hbuz-poll-share-messenger")
      .attr(
        "href",
        "fb-messenger://share/?link=" + encodeURIComponent(pollUrl)
      );

    // Set WhatsApp share link
    $(this)
      .find(".hbuz-poll-share-whatsapp")
      .attr("href", "https://wa.me/?text=" + encodeURIComponent(pollUrl));

    // Set X (Twitter) share link
    $(this)
      .find(".hbuz-poll-share-twitter")
      .attr(
        "href",
        "https://twitter.com/share?url=" + encodeURIComponent(pollUrl)
      );

    // Set LinkedIn share link
    $(this)
      .find(".hbuz-poll-share-linkedin")
      .attr(
        "href",
        "https://www.linkedin.com/shareArticle?mini=true&url=" +
          encodeURIComponent(pollUrl)
      );

    // Handle "Copy Link" functionality
    $(this)
      .find(".hbuz-poll-copy-link-icon")
      .on("click", function () {
        // Copy poll link to clipboard
        var tempInput = $("<input>");
        $("body").append(tempInput);
        tempInput.val(pollUrl).select();
        document.execCommand("copy");
        tempInput.remove();

        // Change icon after copying the link
        $(this).html('<i class="fas fa-check"></i> <p>Link Copied</p>'); // Change the icon to a checkmark or custom message

        // Optional: Reset the icon after a few seconds
        setTimeout(() => {
          $(this).html('<i class="fas fa-link"></i>');
        }, 3000); // Reset after 3 seconds
      });
  });

// Handle the click event on the download button
  $(".hbuz-poll-download-btn").on("click", function () {
    var pollId = $(this).data("poll-id");
    var pollElement = $("#poll-" + pollId); // Get the specific poll element by ID

    // Use html2canvas to capture the element
    html2canvas(pollElement[0], {
      scrollY: -window.scrollY, // Prevents scrolling issues when rendering
    })
      .then(function (canvas) {
        // Convert the canvas to an image
        var imgData = canvas.toDataURL("image/png");

        // Create a temporary link element to trigger the download
        var link = document.createElement("a");
        link.href = imgData;
        link.download = "poll-" + pollId + ".png";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      })
      .catch(function (error) {
        console.error("Error capturing the poll element:", error);
      });
  });


});


