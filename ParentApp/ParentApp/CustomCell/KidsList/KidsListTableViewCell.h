//
//  KidsListTableViewCell.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 10/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface KidsListTableViewCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lbl_viewSex;
@property (weak, nonatomic) IBOutlet UILabel *lbl_viewAge;
@property (weak, nonatomic) IBOutlet AsyncImageView *viewKidsImage;
@property (weak, nonatomic) IBOutlet UILabel *lbl_kidsName;
@property (weak, nonatomic) IBOutlet UILabel *lbl_age;
@property (weak, nonatomic) IBOutlet UILabel *lbl_sex;


@end
