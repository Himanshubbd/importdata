$(document).ready(function () {
  const maxVisibleChoices = 3; // Maximum number of visible selected items
  const $select = $(".chosen-select");

  // Initialize Chosen plugin
  $select.chosen({
    width: "100%",
  });

  // Function to manage the selected tags
  function updateSelectedTags() {
    const $chosenContainer = $select.siblings('.chosen-container');
    const $chosenChoices = $chosenContainer.find('.chosen-choices li.search-choice');
    const visibleChoices = $chosenChoices.filter(':visible');
    const totalChoices = $select.val() ? $select.val().length : 0; // Count selected items from the select element

    $chosenChoices.show();
    $chosenContainer.find('.plus-indicator').remove();

    if (totalChoices > maxVisibleChoices) {
      $chosenChoices.slice(maxVisibleChoices).hide();

      const hiddenCount = totalChoices - maxVisibleChoices;
      const plusIndicator = $(
        `<li class="search-choice plus-indicator"><span>+${hiddenCount}</span></li>`
      );

      $chosenContainer.find('.chosen-choices .search-field').before(plusIndicator);
    }
  }

  $select.on('change chosen:updated', function () {
    // Allow time for Chosen to update the DOM, then execute
    setTimeout(updateSelectedTags, 0);
  });
  updateSelectedTags();
});