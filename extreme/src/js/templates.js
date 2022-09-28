const CARD_TEMPLATE = `
<div class="card">
    <div class="content">
        <div class="header">
            <a href="detail.php?id=%ROUTE_ID%">%ROUTE_NAME%</a>
        </div>
        <div class="meta">
            <span class="date">%ROUTE_CREATED_AT%</span>
        </div>
        <div class="description">%ROUTE_DESCRIPTION%</div>
    </div>
    <div class="extra content">
        <div id="like-button-%ROUTE_ID%">%BUTTON_COMPONENT%</div>
        %MANAGE_COMPONENT%
    </div>
</div>`

const LIKE_BUTTON_BIG_TEMPLATE = `
<div class="ui labeled button" tabindex="0" onclick="%LIKE_STATE_TOGGLE%">
    <div class="ui %LIKE_COLOR% button"><i class="heart icon"></i>いいね</div>
    <a class="ui basic %LIKE_COLOR% left pointing label">%LIKE_COUNT%</a>
</div>
`

const LIKE_BUTTON_TEMPLATE = `
<span class="left floated" onclick="%LIKE_STATE_TOGGLE%">
    <i class="heart %LIKE_COLOR% like icon"></i>
    <span class="ui %LIKE_COLOR%">%LIKE_COUNT% いいね</span>
</span>
`

const MANAGE_COMPONENT_TEMPLATE = `
<span class="right floated">
    <i class="edit icon" onclick="%EDIT_ACTION%"></i>
    <i class="trash icon" onclick="%DELETE_ACTION%"></i>
</span>
`

const PAGINATION_TEMPLATE = `
<a class="item %CLASS%" onclick="%ACTION%">%NUMBER%</a>
`

function setLikeState(id, state, count, isStatic, useBigButton) {
    // いいね数を増減
    state ? count++ : count--

    let likeButton = getLikeButtonTemplate(id, state, count, isStatic, useBigButton)
    $(isStatic ? '#like-button' : `#like-button-${id}`).html(likeButton)

    // APIを叩いてデータベースに反映
    api('POST', state ? 'likePost' : 'unlikePost', { post_id: id })
}

function getLikeButtonTemplate(id, state, count, isStatic = false, useBigButton = false) {
    return (useBigButton ? LIKE_BUTTON_BIG_TEMPLATE : LIKE_BUTTON_TEMPLATE)
        .replace(/%LIKE_STATE_TOGGLE%/g, `setLikeState(${id}, ${!state}, ${count}, ${isStatic}, ${useBigButton})`)
        .replace(/%LIKE_COLOR%/g, state ? 'red' : '')
        .replace(/%LIKE_COUNT%/g, count)
}

function getManageComponentTemplate(id) {
    return MANAGE_COMPONENT_TEMPLATE
        .replace(/%EDIT_ACTION%/g, `location.href = 'editor.php?id=${id}'`)
        .replace(/%DELETE_ACTION%/g, `showDeleteModal(${id})`)
}

function getPaginationTemplate(page, cssClass, text) {
    return PAGINATION_TEMPLATE
        .replace(/%CLASS%/g, cssClass)
        .replace(/%ACTION%/g, cssClass === 'disabled' ? '' : `renderingPosts(${page})`)
        .replace(/%NUMBER%/g, text || page)
}