//
//  ChildProfileImages.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 04/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "AsyncImageView.h"

@interface ChildProfileImages : UIView
@property (weak, nonatomic) IBOutlet UILabel *lbl_childName;
@property (weak, nonatomic) IBOutlet UIImageView *img_checkedChild;

@property (weak, nonatomic) IBOutlet AsyncImageView *view_childImage;

@property (weak, nonatomic) IBOutlet UIButton *btn_selectImage;


@end
