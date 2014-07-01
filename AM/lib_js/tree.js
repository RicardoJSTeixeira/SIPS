var tree = function(selector, data, type_encomenda, parent_id, produtos) {
    var checkbox_count = 1;
    var me = this;
    this.treeRaw = data;
    this.selector = selector;



    function construct(data) {
        var temp = $("<ul>");
        $.each(data.children, function() {
            if (typeof this.children === "object") {
                temp.append(feed(this).find("ul").replaceWith(construct(this)).end());
            } else {
                temp.append(feed(this));
            }
        });
        return temp;
    }


    this.init = function() {
        var UL;
        $.each(me.treeRaw, function() {
            UL = $("<ul>").append(feed(this));
            if (typeof this.children === "object") {
                UL.find("ul").replaceWith(construct(this));
            }
            UL.appendTo(me.selector);
        });
        $(me.selector + " label, " + me.selector + " label > span ," + me.selector + " input[type=checkbox]").click(function(e) {
            e.stopPropagation();
        });
        $(me.selector + " label, " + me.selector + " label > span ," + me.selector + " input[type=number]").click(function(e) {
            e.stopPropagation();
        });
        $(me.selector + " label, " + me.selector + " label > span ," + me.selector + " select").click(function(e) {
            e.stopPropagation();
        });
        startPlugin();
    };

    function feed(data) {

        data = produtos[data.id];
        var hasChilds = false;
        if (typeof data.children === "object") {
            hasChilds = Boolean(data.children.length);
        }
        var color = "";
        if (data.color.length)
            $.each(data.color, function() {
                color += "<option style='background: #" + this.color + "'   value='" + this.color + "'>" + this.name + "</option>";
            })

        var max_value = 1;
        if (type_encomenda == "mensal")
            max_value = data.max_req_m;
        else
            max_value = data.max_req_s;



// METER COR NA OPTION DO SELECT BACKGROUND COLOR
        var quantity_temp = "";
        var size_temp = "";
        if (data.category === "Acessório")
        {
            size_temp = $("<div>", {class: " input-prepend size_div"})
                    .append($("<span>", {class: "add-on"}).text("Size."))
                    .append($("<select>", {class: "input_size input-mini size_" + data.id + "",  data_id: data.id}) .data("data_id", data.id)) ;
             
        }
        if (data.id === parent_id) {
            quantity_temp = $("<div>", {class: " input-prepend quantity_div"})
                    .append($("<span>", {class: "add-on"}).text("Q."))
                    .append($("<input>", {class: "input_quantity input-mini quantity_" + data.id + "", type: "number", min: 1, max: max_value, value: 1, data_id: data.id}).data("last_value", 1).data("data_id", data.id)
                            .on("change", function() {
                                if ($(this).val() > ~~$(this).prop('max')) {
                                    $(this).val(~~$(this).prop('max'));
                                    return false;
                                }
                                if ($(this).val() < ~~$(this).prop('min')) {
                                    $(this).val(~~$(this).prop('min'));
                                    return false;
                                }
                                var lastValue = ~~$(this).data().last_value,
                                        add = (lastValue < ~~$(this).val()) ? 1 : -1;
                                $(this)
                                        .data()
                                        .last_value = $(this).val();
                                $(this)
                                        .closest("li")
                                        .find(".input_quantity")
                                        .not(this)
                                        .map(function() {
                                            if (~~$(this).prop("max") >= (~~$(this).val() + add))
                                                $(this).val(~~$(this).val() + add);
                                        });
                            })).css("display", "inline").hide();
        }
        else {
            quantity_temp = $("<div>", {class: " input-prepend quantity_div"})
                    .append($("<span>", {class: "add-on"}).text("Q."))
                    .append($("<input>", {class: "input_quantity quantity_" + data.id + "", type: "number", min: 1, max: max_value, value: 1, data_id: data.id}).data("data_id", data.id)
                            .on("change", function() {
                                $(".quantity_" + $(this).data().data_id).val($(this).val());
                                if ($(this).val() > ~~$(this).prop('max')) {
                                    $(this).val(~~$(this).prop('max'));
                                    return false;
                                }
                                if ($(this).val() < ~~$(this).prop('min')) {
                                    $(this).val(~~$(this).prop('min'));
                                    return false;
                                }

                            })).css("display", "inline").hide();
        }
        return $("<li>").data("name", data.name)
                .append($("<span>", {class: "product_item"}).prop("id_product", data.id).prop("name_product", data.name).prop("category_product", data.category)
                        .append($("<i>", {class: "icon-minus-sign"}).toggle(hasChilds))

                        .append($("<input>", {type: "checkbox", id: "checkbox" + checkbox_count, "name": "pselector", value: data.id, color: data.color.length}).prop("disabled", max_value < 1 ? true : false))
                        .append($("<label>", {class: "checkbox inline", for : "checkbox" + checkbox_count++}).append($("<span>")))
                        .append(" " + data.name + " ")
                        .append(quantity_temp)
                        .append(size_temp)
                        .append($("<div>", {class: "  input-prepend color_div"})
                                .append($("<span>", {class: "add-on"}).text("Cor"))
                                .append($("<select>", {class: "input-small color_select"}).append(color)).css("display", "inline").hide()))
                .on("change", " > span > input[type=checkbox]", function(e) {

                    if (this.checked) {
                        $(this).closest("li").parents("li").find(" > span > input").prop("checked", true).change();
                        $(this).closest("li").find(".quantity_div").first().show();
                        $(this).closest("li").find(".size_div").first().show();
                        if (~~$(this).attr("color") > 0)
                            $(this).closest("li").find(".color_div").first().show();
                    }
                    else {
                        $(this).closest("li").find("input[name=" + this.name + "]").not(this).prop("checked", this.checked).change();
                        $(this).closest("li").find(".quantity_div").first().hide();
                        $(this).closest("li").find(".size_div").first().hide();
                        $(this).closest("li").find(".color_div").first().hide();
                    }
                    var isClosed = $(this)
                            .closest("li")
                            .find("i")
                            .hasClass("icon-plus-sign");
                    if (isClosed) {
                        $(this).closest("span").click();
                    }
                    $(this).closest("li").data()[this.name] = this.checked;
                })
                .append($("<ul>", {"id": "jj"}));
    }

    function startPlugin() {
        $('.tree > ul').attr('role', 'tree').find('ul').attr('role', 'group');
        $('.tree').find('li:has(ul)').addClass('parent_li').attr('role', 'treeitem').find(' > span').attr('title', 'Collapse this branch').on('click', function(e) {
            var children = $(this).parent('li.parent_li').find(' > ul > li');
            if (children.is(':visible')) {
                children.hide('fast');
                $(this).attr('title', 'Expand this branch').find(' > i').removeClass().addClass('icon-plus-sign');
            } else {
                children.show('fast');
                $(this).attr('title', 'Collapse this branch').find(' > i').removeClass().addClass('icon-minus-sign');
            }
            e.stopPropagation();
        });
    }


    function getChildrenChanges(elm) {
        var children = [], current = {};
        $(elm).find(" > ul > li").each(function() {
            current = {name: $(this).data().name, values: [], children: []};
            current.values = $(this).data();
            current.children = getChildrenChanges(this);
            children.push(current);
        });
        return children;
    }
    this.getChanges = function() {
        var values = [];
        $(me.selector)
                .find(" > ul > li")
                .each(function() {
                    current = {name: $(this).data().name, values: [], children: []};
                    current.values = $(this).data();
                    current.children = getChildrenChanges(this);
                    values.push(current);
                });
        return values;
    };
    function getChildrenValues(elm) {
        var children = [], current = {};
        $(elm).find(" > ul > li").each(function() {
            current = {name: $(this).data().name, values: [], children: []};
            current.values = $(this).find(" > span > div > label > input:checkbox").map(function() {
                return {name: this.name, checked: this.checked};
            });
            current.children = getChildrenValues(this);
            children.push(current);
        });
        return children;
    }
    this.getValues = function() {
        var values = [];
        $(me.selector)
                .find(" > ul > li")
                .each(function() {
                    current = {name: $(this).data().name, values: [], children: []};
                    current.values = $(this).find(" > span > div > label > input:checkbox").map(function() {
                        return {name: this.name, checked: this.checked};
                    });
                    current.children = getChildrenValues(this);
                    values.push(current);
                });
        return values;
    };
    this.destroy = function() {
        $(me.selector).empty();
    };
};