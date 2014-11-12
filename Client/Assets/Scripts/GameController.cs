using UnityEngine;
using System.Collections;
using UnityEngine.UI;

public class GameController : MonoBehaviour {
	public enum GameState{
		imageView,
		imageSelected,
		addLineAttribute
	}

	public Transform targetImagePosition;
	public GameObject radialMenu;

	public Transform linePrefab;

	[HideInInspector]
	public Transform selectedImage;
	private Vector3 selectedImageOrig;
	private Vector3 selectedImageRot;

	private Transform line;

	[HideInInspector]
	public GameState state { 
		get {
			return _state;
		}set{
			_state = value;

			if (_state == GameState.imageView){
				radialMenu.SetActive(false);
			}
			// Do any state specific stuff here
		}
	}
	private GameState _state;

	// Use this for initialization
	void Start () {
		state = GameState.imageView;
	}
	
	// Update is called once per frame
	void Update () {

	}

	// blueberries, layered, bright rocks
	public void ImageTagged(int imgTag = 0){
		Debug.Log("Tagged!");
		state = GameState.imageView;

		if (imgTag == 1){
			state = GameState.addLineAttribute;
			var canvas = selectedImage.FindChild("Canvas");
			canvas.gameObject.SetActive(true);
			startLineEdit();
		} else {
			var mark = selectedImage.FindChild("Mark");
			mark.gameObject.SetActive(true);
			ImageMetadata md = selectedImage.GetComponent<ImageMetadata> ();
			md.SubmitTag (imgTag);
			DeselectImage();
		}
	}

	public void CancelTag(){
		Debug.Log("Cancel!");
		state = GameState.imageView;
		DeselectImage();
	}

	public void SelectImage(Transform image){
		state = GameState.imageSelected;

		selectedImage = image;
		selectedImageOrig = image.position;
		selectedImageRot = image.rotation.eulerAngles;

		Go.to(image, .66f, new GoTweenConfig()
		      .position(targetImagePosition.position)
		      .rotation(-Vector3.forward)
		      .onComplete(thisTransform => {
			radialMenu.SetActive(true);
		}));
	}

	public void startLineEdit(){
		line = (Transform) Instantiate(linePrefab);
		line.GetComponent<LineAttribute>().controller = this;
		line.parent = selectedImage;
		line.transform.localPosition = new Vector3(0,0,-.1f);
	}

	public void endLineEdit(){
		// DO Line Commit stuff here
		var mark = selectedImage.FindChild("Mark");
		mark.gameObject.SetActive(true);
		DeselectImage();
		line = null;
	}

	public void cancelLineEdit(){
		DeselectImage();
		state = GameState.imageView;
		line = null;
	}

	public void DeselectImage(){
		if (line != null){
			Destroy(line.gameObject);
		}

		radialMenu.SetActive(false);
		if (selectedImage != null){
			var canvas = selectedImage.FindChild("Canvas");

			if (canvas != null){
				canvas.gameObject.SetActive(false);
			}

			Go.to(selectedImage, .66f, new GoTweenConfig()
			      .position(selectedImageOrig)
			      .rotation(selectedImageRot));
		}
		selectedImage = null;
	}
}
