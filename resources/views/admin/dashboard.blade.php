<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            記事一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                <section class="text-gray-600 body-font">
  <div class="container px-5 py-5 mx-auto">
    <div class="w-full mx-auto overflow-auto">
      <table class="table-auto w-full text-left whitespace-no-wrap">
        <thead>
          <tr>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">タイトル</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">編集</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">削除</th>
          </tr>
        </thead>
        <tbody class="drag-list">
        @foreach($news as $new)
          <tr id="item_{{$new->id}}" draggable="true">
            <td class="px-4 py-3"><a href="{{route('admin.news.show',['id'=>$new->id])}}">{{$new->title}}</a></td>
            <td class="px-4 py-3"><a href="{{route('admin.news.edit',['id'=>$new->id])}}">編集</a></td>
            <td class="px-4 py-3"><a href="{{route('admin.news.delete',['id'=>$new->id])}}">削除</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="flex pl-4 mt-4 lg:w-2/3 w-full mx-auto">
      <a class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">Learn More
        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
          <path d="M5 12h14M12 5l7 7-7 7"></path>
        </svg>
      </a>
      <button class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">Button</button>
    </div>
  </div>
</section>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.querySelectorAll('.drag-list tr').forEach (elm => {
	elm.ondragstart = function () {
		event.dataTransfer.setData('text/plain', this.id);
	};
	elm.ondragover = function () {
		event.preventDefault();
		let rect = this.getBoundingClientRect();
		if ((event.clientY - rect.top) < (this.clientHeight / 2)) {
			//マウスカーソルの位置が要素の半分より上
			this.style.borderTop = '2px solid blue';
			this.style.borderBottom = '';
		} else {
			//マウスカーソルの位置が要素の半分より下
			this.style.borderTop = '';
			this.style.borderBottom = '2px solid blue';
		}
	};
	elm.ondragleave = function () {
		this.style.borderTop = '';
		this.style.borderBottom = '';
	};
	elm.ondrop = function () {
		event.preventDefault();
		let id = event.dataTransfer.getData('text/plain');
		let elm_drag = document.getElementById(id);

        let rect = this.getBoundingClientRect();
        let side;
		if ((event.clientY - rect.top) < (this.clientHeight / 2)) {
			//マウスカーソルの位置が要素の半分より上
			this.parentNode.insertBefore(elm_drag, this);
            side=1;
        } else {
			//マウスカーソルの位置が要素の半分より下
			this.parentNode.insertBefore(elm_drag, this.nextSibling);
            side=2;
		}
		this.style.borderTop = '';
		this.style.borderBottom = '';
        //ajax通信
        var xhr = new XMLHttpRequest();
            var token = document.getElementsByName('csrf-token').item(0).content;
            xhr.open('POST', '{{route('admin.news.sort')}}');
            xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
            xhr.setRequestHeader('X-CSRF-Token', token); 
            xhr.send( 'side='+side+'&target='+id+'&place='+this.id );
    };
});
</script>
</x-app-layout>
