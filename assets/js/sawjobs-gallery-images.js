(function () {
  "use strict";

  var settings = window.DirectoristSawjobsGallery || {};

  function messageFromResponse(response) {
    if (response && response.data && response.data.message) {
      return response.data.message;
    }

    return settings.genericError || "Something went wrong. Please try again.";
  }

  async function ajaxRequest(formData) {
    var request = await window.fetch(settings.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      body: formData,
    });
    var response;

    try {
      response = await request.json();
    } catch (error) {
      throw new Error(settings.genericError || "Something went wrong. Please try again.");
    }

    if (!request.ok || !response.success) {
      throw new Error(messageFromResponse(response));
    }

    return response.data;
  }

  function showMessage(root, text, type) {
    var message = root.querySelector("[data-gallery-message]");

    if (!message) {
      return;
    }

    message.textContent = text || "";
    message.hidden = !text;
    message.classList.toggle("is-error", type === "error");
    message.classList.toggle("is-success", type === "success");
  }

  function updateDashboardState(root, count, maximum) {
    var countNode = root.querySelector("[data-gallery-count]");
    var limitNode = root.querySelector("[data-gallery-limit]");
    var input = root.querySelector("[data-gallery-file-input]");
    var dropzone = root.querySelector("[data-gallery-dropzone]");
    var empty = root.querySelector("[data-gallery-empty]");
    var isFull = count >= maximum;

    root.dataset.currentCount = String(count);
    root.dataset.maxImages = String(maximum);

    if (countNode) {
      countNode.textContent = String(count);
    }

    if (limitNode) {
      limitNode.textContent = String(maximum);
    }

    if (input) {
      input.disabled = isFull || root.classList.contains("is-busy");
    }

    if (dropzone) {
      dropzone.classList.toggle("is-disabled", isFull);
    }

    if (empty) {
      empty.hidden = count > 0;
    }
  }

  function createDashboardItem(image) {
    var item = document.createElement("article");
    var img = document.createElement("img");
    var remove = document.createElement("button");
    var icon = document.createElement("i");

    item.className = "directorist-sawjobs-gallery-dashboard__item";
    item.dataset.galleryItem = String(image.id);

    img.className = "directorist-sawjobs-gallery-dashboard__image";
    img.src = image.thumbnail || image.full;
    img.alt = image.alt || "";
    img.loading = "lazy";

    remove.type = "button";
    remove.className = "directorist-sawjobs-gallery-dashboard__remove";
    remove.dataset.galleryRemove = String(image.id);
    remove.setAttribute("aria-label", settings.removeLabel || "Remove image permanently");

    icon.className = "las la-trash";
    icon.setAttribute("aria-hidden", "true");
    remove.appendChild(icon);

    item.appendChild(img);
    item.appendChild(remove);

    return item;
  }

  function formatRemainingMessage(remaining) {
    var template = settings.tooManyText || "Only %d more images can be uploaded.";

    return template.replace("%d", String(remaining));
  }

  async function uploadFiles(root, selectedFiles) {
    var input = root.querySelector("[data-gallery-file-input]");
    var grid = root.querySelector("[data-gallery-grid]");
    var current = Number(root.dataset.currentCount || 0);
    var maximum = Number(root.dataset.maxImages || 10);
    var remaining = Math.max(0, maximum - current);
    var files = Array.prototype.slice.call(selectedFiles, 0, remaining);
    var lastMessage = "";
    var uploadFailed = false;

    if (!remaining) {
      showMessage(root, formatRemainingMessage(0), "error");
      return;
    }

    if (selectedFiles.length > remaining) {
      showMessage(root, formatRemainingMessage(remaining), "error");
    } else {
      showMessage(root, settings.uploadingText || "Uploading images...", "");
    }

    root.classList.add("is-busy");

    if (input) {
      input.disabled = true;
    }

    for (var index = 0; index < files.length; index += 1) {
      var data = new window.FormData();

      data.append("action", settings.uploadAction);
      data.append("nonce", settings.nonce);
      data.append("gallery_image", files[index]);

      try {
        var response = await ajaxRequest(data);

        if (grid && response.image) {
          grid.appendChild(createDashboardItem(response.image));
        }

        current = Number(response.count);
        maximum = Number(response.maxImages);
        lastMessage = response.message;
        updateDashboardState(root, current, maximum);
      } catch (error) {
        uploadFailed = true;
        showMessage(root, error.message, "error");
        break;
      }
    }

    root.classList.remove("is-busy");

    if (input) {
      input.value = "";
    }

    updateDashboardState(root, current, maximum);

    if (lastMessage && !uploadFailed) {
      showMessage(root, lastMessage, "success");
    }
  }

  async function removeImage(root, button) {
    var attachmentId = Number(button.dataset.galleryRemove || 0);

    if (!attachmentId || !window.confirm(settings.confirmRemove)) {
      return;
    }

    var data = new window.FormData();
    data.append("action", settings.removeAction);
    data.append("nonce", settings.nonce);
    data.append("attachment_id", String(attachmentId));

    button.disabled = true;

    try {
      var response = await ajaxRequest(data);
      var item = root.querySelector('[data-gallery-item="' + attachmentId + '"]');

      if (item) {
        item.remove();
      }

      updateDashboardState(root, Number(response.count), Number(response.maxImages));
      showMessage(root, response.message, "success");
    } catch (error) {
      button.disabled = false;
      showMessage(root, error.message, "error");
    }
  }

  function initializeDashboard(root) {
    var input = root.querySelector("[data-gallery-file-input]");
    var dropzone = root.querySelector("[data-gallery-dropzone]");

    if (!input || !dropzone || !settings.ajaxUrl || !settings.nonce) {
      return;
    }

    input.addEventListener("change", function () {
      if (input.files && input.files.length) {
        uploadFiles(root, input.files);
      }
    });

    ["dragenter", "dragover"].forEach(function (eventName) {
      dropzone.addEventListener(eventName, function (event) {
        event.preventDefault();

        if (!input.disabled) {
          dropzone.classList.add("is-dragging");
        }
      });
    });

    ["dragleave", "drop"].forEach(function (eventName) {
      dropzone.addEventListener(eventName, function (event) {
        event.preventDefault();
        dropzone.classList.remove("is-dragging");
      });
    });

    dropzone.addEventListener("drop", function (event) {
      if (!input.disabled && event.dataTransfer.files.length) {
        uploadFiles(root, event.dataTransfer.files);
      }
    });

    root.addEventListener("click", function (event) {
      var removeButton = event.target.closest("[data-gallery-remove]");

      if (removeButton && root.contains(removeButton)) {
        removeImage(root, removeButton);
      }
    });
  }

  function initializeLightbox(gallery) {
    var links = Array.prototype.slice.call(gallery.querySelectorAll("[data-gallery-lightbox]"));
    var modal = gallery.querySelector("[data-gallery-modal]");
    var modalImage = gallery.querySelector("[data-gallery-modal-image]");
    var counter = gallery.querySelector("[data-gallery-modal-counter]");
    var closeButton = gallery.querySelector("[data-gallery-close]");
    var previousButton = gallery.querySelector("[data-gallery-previous]");
    var nextButton = gallery.querySelector("[data-gallery-next]");
    var currentIndex = 0;
    var previousFocus = null;

    if (!links.length || !modal || !modalImage || !closeButton) {
      return;
    }

    function showImage(index) {
      currentIndex = (index + links.length) % links.length;

      var link = links[currentIndex];
      var sourceImage = link.querySelector("img");

      modalImage.src = link.href;
      modalImage.alt = sourceImage ? sourceImage.alt : "";

      if (counter) {
        counter.textContent = String(currentIndex + 1) + " / " + String(links.length);
      }
    }

    function openLightbox(index) {
      previousFocus = document.activeElement;
      showImage(index);
      modal.classList.add("is-open");
      modal.setAttribute("aria-hidden", "false");
      document.body.classList.add("directorist-sawjobs-gallery-lightbox-open");
      closeButton.focus();
    }

    function closeLightbox() {
      modal.classList.remove("is-open");
      modal.setAttribute("aria-hidden", "true");
      document.body.classList.remove("directorist-sawjobs-gallery-lightbox-open");
      modalImage.src = "";

      if (previousFocus) {
        previousFocus.focus();
      }
    }

    links.forEach(function (link, index) {
      link.addEventListener("click", function (event) {
        event.preventDefault();
        openLightbox(index);
      });
    });

    closeButton.addEventListener("click", closeLightbox);

    if (previousButton) {
      previousButton.addEventListener("click", function () {
        showImage(currentIndex - 1);
      });
    }

    if (nextButton) {
      nextButton.addEventListener("click", function () {
        showImage(currentIndex + 1);
      });
    }

    modal.addEventListener("click", function (event) {
      if (event.target === modal) {
        closeLightbox();
      }
    });

    document.addEventListener("keydown", function (event) {
      if (!modal.classList.contains("is-open")) {
        return;
      }

      if (event.key === "Escape") {
        closeLightbox();
      } else if (event.key === "ArrowLeft") {
        showImage(currentIndex - 1);
      } else if (event.key === "ArrowRight") {
        showImage(currentIndex + 1);
      }
    });
  }

  function initialize() {
    document.querySelectorAll(".directorist-sawjobs-gallery-dashboard").forEach(initializeDashboard);
    document.querySelectorAll("[data-author-gallery]").forEach(initializeLightbox);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initialize);
  } else {
    initialize();
  }
})();
