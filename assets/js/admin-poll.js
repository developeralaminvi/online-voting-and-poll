// Add JavaScript for accordion functionality

function togglePollContent(pollId) {
  var content = document.getElementById("poll-content-" + pollId);
  if (content.style.display === "none" || content.style.display === "") {
    content.style.display = "block";
  } else {
    content.style.display = "none";
  }
}
