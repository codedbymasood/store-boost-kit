(function($){
  $(document).ready(function() {
    // Color picker
    $(".color-picker").wpColorPicker();
    
    // Tabs
    $(".metabox-tabs .nav-tab").click(function() {
        var target = $(this).data("target");
        $(".metabox-tabs .nav-tab").removeClass("nav-tab-active");
        $(this).addClass("nav-tab-active");
        $(".metabox-tab-content").removeClass("active");
        $("#" + target).addClass("active");
    });
    
    // Media uploader
    $(document).on("click", ".upload-media-button", function(e) {
        e.preventDefault();
        var button = $(this);
        var input = button.siblings("input[type=hidden]");
        var preview = button.siblings(".media-preview");
        
        var mediaUploader = wp.media({
            title: "Select Media",
            button: { text: "Select" },
            multiple: false
        });
        
        mediaUploader.on("select", function() {
            var attachment = mediaUploader.state().get("selection").first().toJSON();
            input.val(attachment.id);
            if (attachment.type === "image") {
                preview.html("<img src=\"" + attachment.url + "\" />");
            } else {
                preview.html("<p>" + attachment.filename + "</p>");
            }
        });
        
        mediaUploader.open();
    });
    
    $(document).on("click", ".remove-media-button", function(e) {
        e.preventDefault();
        $(this).siblings("input[type=hidden]").val("");
        $(this).siblings(".media-preview").html("");
    });
    
    // Repeater
    $(document).on("click", ".add-repeater-item", function() {
        var container = $(this).siblings(".repeater-container");
        var template = container.find(".repeater-template").html();
        var index = container.find(".repeater-item").length;

        console.log(index);
        
        template = template.replace(/\{INDEX_DISPLAY\}/g, index+1);
        container.append(template);
        checkConditions();
    });
    
    $(document).on("click", ".remove-item", function() {
        $(this).closest(".repeater-item").remove();
        checkConditions();
    });
    
    // Conditional fields
    function checkConditions() {
        $("[data-condition]").each(function() {
            var element = $(this);
            var conditions = JSON.parse(element.attr("data-condition"));
            var show = true;
            
            if (Array.isArray(conditions)) {
                // Multiple conditions with AND/OR logic
                var relation = conditions.relation || "AND";
                var results = [];
                
                conditions.conditions.forEach(function(condition) {
                    results.push(checkSingleCondition(condition));
                });
                
                if (relation === "OR") {
                    show = results.some(function(result) { return result; });
                } else {
                    show = results.every(function(result) { return result; });
                }
            } else {
                // Single condition
                show = checkSingleCondition(conditions);
            }
            
            if (show) {
                element.removeClass("hidden");
            } else {
                element.addClass("hidden");
            }
        });
    }
    
    function checkSingleCondition(condition) {
        var field = $("[name*=\"[" + condition.field + "]\"]");
        if (!field.length) return false;
        
        var fieldValue = "";
        if (field.is(":checkbox")) {
            fieldValue = field.is(":checked") ? "1" : "0";
        } else if (field.is(":radio")) {
            fieldValue = field.filter(":checked").val() || "";
        } else {
            fieldValue = field.val();
        }
        
        var conditionValue = condition.value;
        var operator = condition.operator || "=";
        
        switch (operator) {
            case "=":
                return fieldValue == conditionValue;
            case "!=":
                return fieldValue != conditionValue;
            case "in":
                return Array.isArray(conditionValue) && conditionValue.includes(fieldValue);
            case "not_in":
                return Array.isArray(conditionValue) && !conditionValue.includes(fieldValue);
            default:
                return fieldValue == conditionValue;
        }
    }
    
    // Trigger condition check on field changes
    $(document).on("change", "input, select, textarea", function() {
        checkConditions();
    });
    
    // Initial condition check
    checkConditions();
  });
})(jQuery);

