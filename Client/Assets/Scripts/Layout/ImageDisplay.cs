using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class ImageDisplay : MonoBehaviour {

	private float MinDepth = 13;
	private float MaxDepth = 13;
	private int RowCount = 4;
	private int ColCount = 12;

	private float width = 120;
	private float height = 20;

	private float gridSizeX;
	private float gridSizeY;

	public Dictionary<GameObject, Vector2> objectLocations;
	public GameObject[,] locationMap;

	public ImageLoader imageLoader;

	public Transform playerOrigin;

	// Use this for initialization
	void Start () {
//		height = ((float)width) / ColCount * RowCount;

		Random.seed = (int)System.DateTime.Now.Ticks;
		locationMap = new GameObject[RowCount, ColCount];
		objectLocations = new Dictionary<GameObject, Vector2>();
		Initialize ();

		imageLoader.LoadRandomImages ((string imageUrl) => {
			StartCoroutine(LoadOneImage (imageUrl));
		}, RowCount * ColCount);
	}
	
	// Update is called once per frame
	void Update () {
	
	}

	public void ClearDisplay() {
		foreach (GameObject go in objectLocations.Keys) {
			// TODO animate out or something cool
			go.transform.parent = null;
		}
		locationMap = new GameObject[RowCount, ColCount];
		objectLocations = new Dictionary<GameObject, Vector2>();
	}

	void Initialize() {
		float c = 2 * Mathf.PI * MinDepth;
		gridSizeX = c * width / 360 / RowCount;
		gridSizeY = gridSizeX;
	}

	IEnumerator LoadOneImage(string url) {
		if (AnyEmptyLocations ()) {
			GameObject go = imageLoader.CreateGameObject ();
			WWW www = new WWW (url);
			yield return www;

			imageLoader.SetTexture(go, www);
			if (AnyEmptyLocations ()) {
				Vector2 location = EmptyLocation ();
				PlaceImage ((int)location.y, (int)location.x, go);
			}
		}
	}

	void PlaceImage(int row, int col, GameObject go) {
		go.transform.parent = transform;
		locationMap [row, col] = go;
		objectLocations [go] = new Vector2 (col, row);

		float x = Mathf.Deg2Rad * ((ColCount-1f) / 2.0f - col) / ColCount * 2 * width + Mathf.PI/2;
		float y = ((RowCount-1f) / 2.0f - row) / RowCount * height;
		float depth = MinDepth + Random.value * (MaxDepth - MinDepth);


		Vector3 p = new Vector3 (Mathf.Cos (x), 0, Mathf.Sin (x));
		p *= depth;
		p.y = y;
		go.transform.localPosition = p;

		go.transform.rotation = Quaternion.LookRotation(go.transform.position - transform.position);

		// todo
		go.transform.localScale = new Vector3 (4, 4, 1);
	}
	
	Vector2 EmptyLocation() {
		int attempts = 50;
		while (attempts-- > 0) {
			int r = Random.Range (0, RowCount-1);
			int c = Random.Range (0, ColCount-1);
			if (locationMap[r, c] == null) {
				return new Vector2(c, r);
			}
		}

		for (int r=0; r<RowCount; r++) {
			for (int c=0; c<RowCount; c++) {
				if (locationMap[r, c] == null) {
					return new Vector2(c, r);
				}
			}
		}
		print ("oops");
		return new Vector2(0,0);
	}

	bool AnyEmptyLocations() {
		return objectLocations.Count < RowCount * ColCount;
	}
}
