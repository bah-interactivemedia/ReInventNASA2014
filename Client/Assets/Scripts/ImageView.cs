using UnityEngine;
using UnityEngine.UI;
using System.Collections;

public class ImageView : MonoBehaviour {
	public Text text;
	public Canvas canvas;

	public void ShowMessage(string message){
		canvas.gameObject.SetActive(true);
		text.text = message;

	}

	public void HideMessage(){
		canvas.gameObject.SetActive(false);
	}
}
