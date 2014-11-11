using UnityEngine;
using System.Collections;

public class ImageLoader : MonoBehaviour {

	public const float ImageScale = 1/100f;
	public GameObject prefabImage;

	// Use this for initialization
	void Start () {

	}
	
	// Update is called once per frame
	void Update () {
	
	}

	public GameObject CreateGameObject() {
		GameObject go = (GameObject)GameObject.Instantiate (prefabImage);
		return go;
	}

	public WWW GetWWWForImage(string image) {
		var path = "file:///"+Application.streamingAssetsPath + "/TestImages/" + image;
		print (path);
		return new WWW (path);
	}

	public string GetRandomImage() {
		int index = Random.Range (0, TestImages.List.Length - 1);
		return TestImages.List[index];
	}

	public void SetTexture(GameObject go, WWW www) {
		Texture2D tex = www.texture;
		print (tex);
		go.renderer.material.SetTexture ("_MainTex", tex);
		go.transform.localScale = new Vector3 (tex.width * ImageScale,tex.height * ImageScale,1);
	}
}
