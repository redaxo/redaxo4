<SCRIPT LANGUAGE="JavaScript">
<!--
// Shopping Cart functions
// Copyright 2004 Carsten Eckelmann
// <careck@circle42.com>, http://circle42.com

// The shopping cart is stored in a cookie
// of the name 'cart' with a value of the form
// <id><amount><id><amount>... with
// <id> = 3 characters
// <amount> = 2 characters, e.g.
// cart=013021041200301

function addItem(item, amount)
{
  var oldAmount = getAmount(item);
	if (oldAmount)
	{
	  changeAmount(item, amount+oldAmount);
	}
	else {
		setCookie("cart",fillUp(item,3),fillUp(amount,2));
	}
}

function removeItem(item)
{
}

function changeAmount(item, amount)
{
}

function getAmount(item)
{
}

-->
</SCRIPT>
