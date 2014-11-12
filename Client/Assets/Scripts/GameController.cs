using UnityEngine;
using System.Collections;
using UnityEngine.UI;

public class GameController : MonoBehaviour {
	public enum GameState{
		imageView,
		imageInspect,
		imageSelected,
		addLineAttribute
	}

	public Transform targetImagePosition;
	public GameObject radialMenu;

	public Transform linePrefab;
	public Transform rectPrefab;

	[HideInInspector]
	public Transform selectedImage;
	private Vector3 selectedImageOrig;
	private Vector3 selectedImageRot;

	private Transform line;
	private Transform rectangle;

	[HideInInspector]
	public GameState state { 
		get {
			return _state;
		}set{
			_state = value;

			if (_state == GameState.imageView){
				radialMenu.SetActive(false);
			} else if (state == GameState.imageSelected){
				radialMenu.SetActive(true);
				selectedImage.GetComponent<ImageView>().HideMessage();
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
		} else if (imgTag == 3){
			state = GameState.addLineAttribute;
			var canvas = selectedImage.FindChild("Canvas");
			canvas.gameObject.SetActive(true);
			startRectEdit();
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
		state = GameState.imageInspect;

		selectedImage = image;
		selectedImageOrig = image.position;
		selectedImageRot = image.rotation.eulerAngles;

		Go.to(image, .66f, new GoTweenConfig()
		      .position(targetImagePosition.position)
		      .rotation(-Vector3.forward)
		      .onComplete(thisTransform => {
			selectedImage.GetComponent<ImageView>().ShowMessage("Press X to tag image\nPress O to go back");
		}));
	}

	public void startLineEdit(){
		line = (Transform) Instantiate(linePrefab);
		line.GetComponent<LineAttribute>().controller = this;
		line.parent = selectedImage;
		line.transform.localPosition = new Vector3(0,0,-.1f);

		selectedImage.GetComponent<ImageView>().ShowMessage("Left Stick Moves, Triggers rotates, Right Stick up/down scales Press X to place line. Press O to cancel.");
	}

	public void startRectEdit(){
		rectangle = (Transform) Instantiate(rectPrefab);
		rectangle.GetComponent<Rectangle>().controller = this;
		rectangle.parent = selectedImage;
		rectangle.transform.localPosition = new Vector3(0,0,-.1f);
		rectangle.GetComponent<Rectangle>().origin = new Vector3(0,0,-.1f); 
		
		selectedImage.GetComponent<ImageView>().ShowMessage("Left stick moves, Right stick scales.");
	}

	public void endLineEdit(){
		// DO Line Commit stuff here
		var mark = selectedImage.FindChild("Mark");
		mark.gameObject.SetActive(true);
		LineAttribute la = line.GetComponent<LineAttribute> ();
		ImageMetadata md = selectedImage.GetComponent<ImageMetadata> ();
		md.SubmitLine (la.TopLeft (), la.BottomRight ());
		DeselectImage();
	}

	public void endRectEdit(){
		// DO Line Commit stuff here
		var mark = selectedImage.FindChild("Mark");
		mark.gameObject.SetActive(true);
		DeselectImage();
	}

	public void cancelLineEdit(){
		DeselectImage();
		state = GameState.imageView;
	}

	public void cancelRectEdit(){
		DeselectImage();
		state = GameState.imageView;
	}

	public void DeselectImage(){
		if (line != null){
			Destroy(line.gameObject);
		}

		if (rectangle != null){
			Destroy(rectangle.gameObject);
		}

		radialMenu.SetActive(false);
		if (selectedImage != null){
			selectedImage.GetComponent<ImageView>().HideMessage();

			Go.to(selectedImage, .66f, new GoTweenConfig()
			      .position(selectedImageOrig)
			      .rotation(selectedImageRot));
		}
		selectedImage = null;
	}
}
