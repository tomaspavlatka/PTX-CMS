// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Html tags
// http://en.wikipedia.org/wiki/html
// ----------------------------------------------------------------------------
// Basic set. Feel free to add more tags
// ----------------------------------------------------------------------------
mySettings = {
	onShiftEnter:	{keepDefault:false, replaceWith:'<br />\n'},
	onCtrlEnter:	{keepDefault:false, openWith:'\n<p>', closeWith:'</p>\n'},
	onTab:			{keepDefault:false, openWith:'	 '},
	markupSet: [
    {name:'Heading 1', key:'1', openWith:'<h1(!( class="[![Class]!]")!)>', closeWith:'</h1>', placeHolder:'Your title here...' },
		{name:'Heading 2', key:'2', openWith:'<h2(!( class="[![Class]!]")!)>', closeWith:'</h2>', placeHolder:'Your title here...' },
		{name:'Heading 3', key:'3', openWith:'<h3(!( class="[![Class]!]")!)>', closeWith:'</h3>', placeHolder:'Your title here...' },
		{name:'Heading 4', key:'4', openWith:'<h4(!( class="[![Class]!]")!)>', closeWith:'</h4>', placeHolder:'Your title here...' },
		{name:'Heading 5', key:'5', openWith:'<h5(!( class="[![Class]!]")!)>', closeWith:'</h5>', placeHolder:'Your title here...' },
		{name:'Heading 6', key:'6', openWith:'<h6(!( class="[![Class]!]")!)>', closeWith:'</h6>', placeHolder:'Your title here...' },
		{name:'Paragraph', openWith:'<p(!( class="[![Class]!]")!)>', closeWith:'</p>' },
		{separator:'---------------' },
		{name:'Bold', key:'B', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)' },
		{name:'Italic', key:'I', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)' },
		{name:'Stroke through', key:'S', openWith:'<del>', closeWith:'</del>' },
		{separator:'---------------' },
		{name:'Ul', openWith:'<ul>\n', closeWith:'</ul>\n' },
		{name:'Ol', openWith:'<ol>\n', closeWith:'</ol>\n' },
		{name:'Li', openWith:'<li>', closeWith:'</li>' },
		{separator:'---------------' },
		{name:'Picture', key:'P', replaceWith:'<img src="[![Source:!:http://]!]" alt="[![Alternative text]!]" />' },
		{name:'Link', key:'L', openWith:'<a href="[![Link:!:http://]!]"(!( title="[![Title]!]")!)>', closeWith:'</a>', placeHolder:'Your text to link...' },
		{separator:'---------------' }, 
		{name:'Lowercase', className:'lowercase', key:'L', replaceWith:'text-transform:lowercase;'},
		{name:'Uppercase', className:'uppercase', key:'U', replaceWith:'text-transform:uppercase;'},
		{separator:'---------------' },
		{name:'Text indent', className:'indent', openWith:'text-indent:', placeHolder:'5px', closeWith:';' },
		{name:'Letter spacing', className:'letterspacing', openWith:'letter-spacing:', placeHolder:'5px', closeWith:';' },
		{name:'Line height', className:'lineheight', openWith:'line-height:', placeHolder:'1.5', closeWith:';' },
		{separator:'---------------' },
    {name:'Alignments', className:'alignments', dropMenu:[
			{name:'Left', className:'left', replaceWith:'text-align:left;'},
			{name:'Center', className:'center', replaceWith:'text-align:center;'},
			{name:'Right', className:'right', replaceWith:'text-align:right;'},
			{name:'Justify', className:'justify', replaceWith:'text-align:justify;'}
			]
		},
		{name:'Padding/Margin', className:'padding', dropMenu:[
				{name:'Top', className:'top', openWith:'(!(padding|!|margin)!)-top:', placeHolder:'5px', closeWith:';' },
				{name:'Left', className:'left', openWith:'(!(padding|!|margin)!)-left:', placeHolder:'5px', closeWith:';' },
				{name:'Right', className:'right', openWith:'(!(padding|!|margin)!)-right:', placeHolder:'5px', closeWith:';' },
				{name:'Bottom', className:'bottom', openWith:'(!(padding|!|margin)!)-bottom:', placeHolder:'5px', closeWith:';' }
			]
		},
		{separator:'---------------' },
		{name:'Clean', className:'clean', replaceWith:function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") } },
		{name:'Preview', className:'preview', call:'preview' },
		{name:'Class', className:'class', key:'N', placeHolder:'Properties here...', openWith:'.[![Class name]!] {\n', closeWith:'\n}'},
	]
}