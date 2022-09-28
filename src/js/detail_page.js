window.onload = () => {
    getPostData()
}

async function getPostData() {
    let id = new URLSearchParams(location.search).get('id')
    let params = {id}
    let res = await api('GET', 'getPost', params)
    console.log(res)

    $('#post-title').text(res.todo)
    $('#post-created-at').text(res.created_at)
    $('#post-updated-at').text(res.updated_at)
    $('#post-url').attr('href', res.url)
    $('#like-button').html(getLikeButtonTemplate(res.id, res.is_liked, res.like_count, true, true))
}